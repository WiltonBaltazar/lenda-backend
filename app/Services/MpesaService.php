<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    private string $apiKey;
    private string $publicKeyStr;
    private string $baseUrl;
    private string $serviceProviderCode;

    public function __construct()
    {
        $this->apiKey = config('mpesa.api_key');
        $this->publicKeyStr = config('mpesa.public_key');
        $this->baseUrl = config('mpesa.base_url');
        $this->serviceProviderCode = config('mpesa.service_provider_code');
    }

    /**
     * Initiate a C2B Payment
     */
    // app/Services/MpesaService.php
    // app/Services/MpesaService.php

    // app/Services/MpesaService.php

    public function initiatePayment(string $phoneNumber, float $amount, string $reference)
    {
        $endpoint = '/ipg/v1x/c2bPayment/singleStage/';
        $baseUrl = str_replace('http://', 'https://', $this->baseUrl);
        $bearerToken = $this->generateAuthorizationToken();

        if (!$bearerToken) {
            return ['success' => false, 'message' => 'Failed to generate security token'];
        }

        // 1. Force Formatting
        $formattedAmount = number_format($amount, 2, '.', '');
        $formattedPhone = $this->formatPhoneNumber($phoneNumber); // "25884..."

        $payload = [
            "input_TransactionReference" => $reference,
            "input_CustomerMSISDN" => $formattedPhone,
            "input_Amount" => $formattedAmount,
            "input_ThirdPartyReference" => $reference,
            "input_ServiceProviderCode" => $this->serviceProviderCode,
        ];

        try {
            // 2. LOG THE PAYLOAD (Check your laravel.log for this!)
            Log::info("Mpesa Request Payload:", $payload);

            $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Origin' => '*',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($baseUrl . $endpoint, $payload);

            Log::info("Mpesa Response Body: " . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['output_ResponseCode']) && $data['output_ResponseCode'] === 'INS-0') {
                    return [
                        'success' => true,
                        'data' => $data,
                        'transaction_id' => $data['output_TransactionID'] ?? $reference
                    ];
                }
                return [
                    'success' => false,
                    'message' => $data['output_ResponseDesc'] ?? 'Declined by M-Pesa'
                ];
            }

            return ['success' => false, 'message' => 'Gateway Error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error("Mpesa Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Query the status of a specific transaction
     * Adapted from Java SDK: APIMethodType.GET to /ipg/v1x/queryTransactionStatus/
     * * @param string $queryReference The M-Pesa Transaction ID (e.g., 5C1400CVRO)
     * @param string $thirdPartyReference Your System Reference (e.g., 111PA2D)
     * @return array
     */
    public function queryTransactionStatus(string $queryReference, string $thirdPartyReference)
    {
        $endpoint = '/ipg/v1x/queryTransactionStatus/';

        // Force HTTPS if defined in config logic, consistent with your existing code
        $baseUrl = str_replace('http://', 'https://', $this->baseUrl);

        $bearerToken = $this->generateAuthorizationToken();

        if (!$bearerToken) {
            return ['success' => false, 'message' => 'Failed to generate security token'];
        }

        // Java: context.addParameter(...)
        // In Laravel Http::get(), the second argument is the array of query parameters
        $queryParams = [
            "input_ThirdPartyReference" => $thirdPartyReference,
            "input_QueryReference" => $queryReference,
            "input_ServiceProviderCode" => $this->serviceProviderCode,
        ];

        try {
            Log::info("Attempting Mpesa Query Status to: " . $baseUrl . $endpoint);

            $response = Http::withOptions([
                'verify' => false,       // SSL verification disabled (per your Sandbox settings)
                'debug' => true,
                'connect_timeout' => 10,
                'timeout' => 30,
            ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Origin' => '*',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->retry(0)
                // Java SDK uses APIMethodType.GET, so we use ->get()
                ->get($baseUrl . $endpoint, $queryParams);

            Log::info("Mpesa Query Response Status: " . $response->status());
            Log::info("Mpesa Query Response Body: " . $response->body());

            if ($response->successful()) {
                $data = $response->json();

                // Check for successful response code (INS-0 is standard success)
                if (isset($data['output_ResponseCode']) && $data['output_ResponseCode'] === 'INS-0') {
                    return [
                        'success' => true,
                        'data' => $data,
                        'status' => $data['output_ResponseTransactionStatus'] ?? 'Unknown'
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['output_ResponseDesc'] ?? 'Transaction query failed'
                ];
            }

            return [
                'success' => false,
                'message' => 'Query failed: ' . $response->body()
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Mpesa Query Connection Refused: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Could not connect to M-Pesa. Firewall or Port issue.'
            ];
        } catch (\Exception $e) {
            Log::error("Mpesa Query General Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'M-Pesa Query error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Initiate a B2B Payment
     * Endpoint: /ipg/v1x/b2bPayment/
     */
    public function b2bPayment(string $amount, string $receiverPartyCode, string $reference, string $thirdPartyReference)
    {
        $endpoint = '/ipg/v1x/b2bPayment/';

        // 1. Force HTTPS
        $url = str_replace('http://', 'https://', $this->baseUrl);

        // 2. FORCE PORT 18349 (Critical for B2B)
        // This swaps out the C2B port (18352) or B2C port (18345) for the correct B2B port.
        if (preg_match('/183(52|45|54)/', $url)) {
            $url = preg_replace('/183(52|45|54)/', '18349', $url);
        }

        $bearerToken = $this->generateAuthorizationToken();

        if (!$bearerToken) {
            return ['success' => false, 'message' => 'Failed to generate security token'];
        }

        $payload = [
            "input_TransactionReference" => $reference,
            "input_Amount" => (string)$amount,
            "input_ThirdPartyReference" => $thirdPartyReference,
            "input_PrimaryPartyCode" => $this->serviceProviderCode, // 171717
            "input_ReceiverPartyCode" => $receiverPartyCode,        // Try 979797 if 902809 fails
        ];

        try {
            // Log the URL to verify we are hitting :18349
            Log::info("Attempting Mpesa B2B at: " . $url . $endpoint);

            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Origin' => '*',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url . $endpoint, $payload);

            Log::info("Mpesa B2B Response: " . $response->body());

            if ($response->successful()) {
                $data = $response->json();

                // INS-0 means accepted/successful
                if (isset($data['output_ResponseCode']) && $data['output_ResponseCode'] === 'INS-0') {
                    return [
                        'success' => true,
                        'data' => $data,
                        'transaction_id' => $data['output_TransactionID'] ?? null
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['output_ResponseDesc'] ?? 'B2B Failed'
                ];
            }

            return ['success' => false, 'message' => 'Request failed: ' . $response->body()];
        } catch (\Exception $e) {
            Log::error("Mpesa B2B Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Initiate a B2C Payment (Business to Customer)
     * Endpoint: /ipg/v1x/b2cPayment/
     * Port (Sandbox): 18345
     */
    public function b2cPayment(string $phoneNumber, float $amount, string $reference, string $thirdPartyReference)
    {
        $endpoint = '/ipg/v1x/b2cPayment/';

        // 1. Force HTTPS
        $url = str_replace('http://', 'https://', $this->baseUrl);

        // 2. Handle Sandbox Port Specifics (18345 for B2C)
        // If your .env has port 18352 (C2B), we try to swap it for 18345 for this call.
        if (str_contains($url, '18352') || str_contains($url, '18349')) {
            $url = str_replace(['18352', '18349'], '18345', $url);
        }

        $bearerToken = $this->generateAuthorizationToken();

        if (!$bearerToken) {
            return ['success' => false, 'message' => 'Failed to generate security token'];
        }

        $payload = [
            "input_TransactionReference" => $reference,
            "input_CustomerMSISDN" => $this->formatPhoneNumber($phoneNumber), // e.g. 258841234567
            "input_Amount" => (string)$amount,
            "input_ThirdPartyReference" => $thirdPartyReference,
            "input_ServiceProviderCode" => $this->serviceProviderCode,
        ];

        try {
            Log::info("Attempting Mpesa B2C: " . $url . $endpoint);

            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Origin' => '*',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url . $endpoint, $payload);

            Log::info("Mpesa B2C Response: " . $response->body());

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['output_ResponseCode']) && $data['output_ResponseCode'] === 'INS-0') {
                    return [
                        'success' => true,
                        'data' => $data,
                        'transaction_id' => $data['output_TransactionID'] ?? null
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['output_ResponseDesc'] ?? 'B2C Failed'
                ];
            }

            return ['success' => false, 'message' => 'Request failed: ' . $response->body()];
        } catch (\Exception $e) {
            Log::error("Mpesa B2C Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reverse a successful transaction
     * Endpoint: /ipg/v1x/reversal/
     * Method: PUT
     * Port (Sandbox): 18354
     */
    public function reverseTransaction(
        string $transactionId,
        float $amount,
        string $thirdPartyReference,
        string $securityCredential = 'Mpesa2019', // Default Sandbox Credential
        string $initiatorIdentifier = 'MPesa2018' // Default Sandbox Initiator
    ) {
        $endpoint = '/ipg/v1x/reversal/';

        // 1. Force HTTPS
        $url = str_replace('http://', 'https://', $this->baseUrl);

        // 2. Handle Sandbox Port (18354 for Reversals)
        // Replaces common ports (18352/18349/18345) with 18354
        if (preg_match('/183(52|49|45)/', $url)) {
            $url = preg_replace('/183(52|49|45)/', '18354', $url);
        }

        $bearerToken = $this->generateAuthorizationToken();

        if (!$bearerToken) {
            return ['success' => false, 'message' => 'Failed to generate security token'];
        }

        $payload = [
            "input_TransactionID" => $transactionId,
            "input_SecurityCredential" => $securityCredential,
            "input_InitiatorIdentifier" => $initiatorIdentifier,
            "input_ThirdPartyReference" => $thirdPartyReference,
            "input_ServiceProviderCode" => $this->serviceProviderCode,
            "input_ReversalAmount" => (string)$amount,
        ];

        try {
            Log::info("Attempting Mpesa Reversal: " . $url . $endpoint);

            $response = Http::withOptions([
                'verify' => false,
                'connect_timeout' => 10,
                'timeout' => 60,
            ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                    'Origin' => '*',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->put($url . $endpoint, $payload); // NOTE: Reversal uses PUT

            Log::info("Mpesa Reversal Response: " . $response->body());

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['output_ResponseCode']) && $data['output_ResponseCode'] === 'INS-0') {
                    return [
                        'success' => true,
                        'data' => $data,
                        'transaction_id' => $data['output_TransactionID'] ?? null
                    ];
                }

                return [
                    'success' => false,
                    'message' => $data['output_ResponseDesc'] ?? 'Reversal Failed'
                ];
            }

            return ['success' => false, 'message' => 'Request failed: ' . $response->body()];
        } catch (\Exception $e) {
            Log::error("Mpesa Reversal Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Encrypts the API Key using the Public Key to create the Session ID/Token
     */
    private function generateAuthorizationToken(): ?string
    {
        try {
            // 1. Clean the key: remove headers if they exist in the env, remove spaces/newlines
            $cleanKey = trim(str_replace(
                ['-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----', "\n", "\r", " "],
                '',
                $this->publicKeyStr
            ));

            // 2. Re-format specifically for OpenSSL (64 chars per line)
            $formattedKey = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($cleanKey, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";

            // 3. Validate the key resource
            $publicKeyResource = openssl_get_publickey($formattedKey);
            if (!$publicKeyResource) {
                Log::error("Mpesa Token Error: Invalid Public Key format.");
                return null;
            }

            // 4. Encrypt using PKCS1 padding (Standard for M-Pesa)
            $encrypted = '';
            $success = openssl_public_encrypt(
                $this->apiKey,
                $encrypted,
                $publicKeyResource,
                OPENSSL_PKCS1_PADDING
            );

            if ($success) {
                return base64_encode($encrypted);
            }

            Log::error("Mpesa Token Error: OpenSSL encryption failed: " . openssl_error_string());
            return null;
        } catch (\Exception $e) {
            Log::error("Mpesa Token Gen Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ensure phone number is in 258xxxxxxxxx format
     */
    private function formatPhoneNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (strlen($number) === 9) {
            return '258' . $number;
        }

        return $number;
    }
}
