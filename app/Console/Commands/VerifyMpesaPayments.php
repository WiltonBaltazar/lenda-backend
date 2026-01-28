<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\MpesaService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class VerifyMpesaPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:verify-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies optimistic M-Pesa transactions and updates their subscription status if payment is invalid';

    /**
     * Execute the console command.
     */
    public function handle(MpesaService $mpesaService)
    {
        $this->info('Starting M-Pesa payments verification...');

        //1. Find subscription created in the last 24 hours that are marked as PAID
        //We skip very recent ones (e.g., < 2mins) to give the user time to type the PIN
        $subscriptions = Subscription::where('payment_status', 'paid')
            ->where('status', 'active')
            ->whereBetween('created_at', [
                Carbon::now()->subHours(24),
                Carbon::now()->subMinutes(2)
            ])
            ->get();

        $count = $subscriptions->count();
        $this->info("Found $count subscriptions to verify");

        foreach ($subscriptions as $subscription) {
            $this->info("Verifying subscription ID {$subscription->id} | Ref: {$subscription->reference}");

            //2. Call the Query API
            $response = $mpesaService->queryTransactionStatus(
                $subscription->mpesa_transaction_id ?? $subscription->payment_reference,
                $subscription->reference
            );

            if (!$response['success']) {
                $this->error(" - API Call Failed: " . $response['message']);
                continue;
            }

            // 3. Analyze the Status
            $mpesaStatus = strtoupper($response['status']); // e.g., 'COMPLETED', 'FAILED', 'CANCELLED'
            $this->comment(" - M-Pesa Status: {$mpesaStatus}");

            // 4. Update Database if Failed
            // Adjust these status strings based on what your specific M-Pesa API returns
            if (in_array($mpesaStatus, ['FAILED', 'CANCELLED', 'REVERSED', 'INS-996'])) {

                $subscription->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'notes' => "Auto-cancelled by system. M-Pesa Status: {$mpesaStatus}"
                ]);

                // Optional: Deactivate the user account too if needed
                // $sub->user->update(['is_active' => false]);

                $this->warn(" - Subscription cancelled.");
            } else {
                $this->info(" - Verified Valid.");
            }
        }
        $this->info('Verification complete.');
    }
}
