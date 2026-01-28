<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\MpesaService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    protected MpesaService $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    public function store(Request $request)
    {
        // 1. Validate Input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|min:9',
            'password' => 'required|string|min:8', // confirmed is optional depending on frontend
            'plan_id' => 'required|exists:plans,id',
        ]);

        // 2. Fetch Plan (Using your logic)
        $plan = Plan::findOrFail($request->plan_id);

        if (!$plan->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'The selected plan is not valid.',
            ], 400);
        }

        // 3. Generate Reference & Initiate M-Pesa Payment
        $reference = 'T' . strtoupper(Str::random(7)); // Keep alphanumeric for Sandbox

        // Call the service
        $paymentResult = $this->mpesa->initiatePayment(
            $validated['phone_number'],
            $plan->price,
            $reference
        );

        // 4. Check Payment Result
        if (!$paymentResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Registration aborted.',
                'error' => $paymentResult['message']
            ], 402); // 402 Payment Required
        }

        // 5. Run Your Registration Logic
        try {
            return DB::transaction(function () use ($request, $plan, $paymentResult, $reference) {

                // A. Create User
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                ]);

                $user->forceFill(['email_verified_at' => now()])->save();

                // B. Dates & C. Subscription (Keep your existing code here)
                $startDate = Carbon::now();
                $days = $plan->duration_days > 0 ? $plan->duration_days : 30;
                $endDate = $startDate->copy()->addDays($days);

                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'payment_reference' => $reference,
                    'mpesa_transaction_id' => $paymentResult['transaction_id'] ?? null,
                ]);

                // --- NEW CODE STARTS HERE ---

                // D. Send Welcome Email
                // Using 'queue' instead of 'send' makes the response faster for the user
                try {
                    Mail::to($user->email)->queue(new WelcomeUser($user, $plan, $subscription));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send welcome email: " . $e->getMessage());
                }

                // --- NEW CODE ENDS HERE ---

                // E. Create Authentication Token
                $token = $user->createToken('auth_token')->plainTextToken;

                // F. Return Your Exact JSON Response
                return response()->json([
                    'success' => true,
                    'message' => 'Registration and payment successful.',
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        // 'full_name' => $user->fullName, // Use this if you have the Attribute in User model
                        'email_verified' => $user->hasVerifiedEmail(),
                    ],
                    'token' => $token,
                ], 201);
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error("Registration DB Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment succeeded but registration failed. Please contact support.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
