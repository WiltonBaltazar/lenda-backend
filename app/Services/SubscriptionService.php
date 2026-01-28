<?php

namespace App\Services;

use App\Exceptions\LendaException;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionService
{
    public function __construct(
        protected MpesaService $mpesaService
    ) {}

    /**
     * Renew the user's last subscription
     */
    public function renewSubscription(User $user, string $mpesaContact): Subscription
    {
        // 1. Get the last subscription (to know which plan to renew)
        $lastSubscription = $user->subscriptions()->latest()->first();

        if (!$lastSubscription) {
            throw new LendaException("Nenhuma subscrição anterior encontrada para renovar.", 404);
        }

        $plan = $lastSubscription->plan;
        
        // 2. Generate Short Unique Reference (Fixes INS-21 Error)
        // Format: "R" + 7 alphanumeric chars (e.g., R1A2B3C4)
        $reference = 'R' . strtoupper(Str::random(7)); 

        // 3. Initiate Payment
        $paymentResponse = $this->mpesaService->initiatePayment(
            phoneNumber: $mpesaContact,
            amount: $plan->price,
            reference: $reference
        );

        if (!$paymentResponse['success']) {
            throw new LendaException("Falha no pagamento M-Pesa: " . ($paymentResponse['message'] ?? 'Erro desconhecido'), 400);
        }

        // 4. Calculate Dates (Logic to ensure validity)
        $now = Carbon::now();
        $startDate = $now;

        // If the user currently has an ACTIVE subscription that hasn't ended yet,
        // we schedule the new one to start exactly when the old one ends.
        if ($lastSubscription->end_date && Carbon::parse($lastSubscription->end_date)->isFuture()) {
            $startDate = Carbon::parse($lastSubscription->end_date);
        }

        // 5. Create Subscription as ACTIVE immediately
        // Note: In a strict environment, you might keep this 'pending' until the callback.
        // For this implementation, we mark it 'active' so the user sees the renewal immediately.
        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',          // <--- Changed from 'pending' to 'active'
            'payment_status' => 'paid',    // <--- Changed from 'pending' to 'paid'
            'payment_method' => 'mpesa',
            'mpesa_reference' => $reference,
            'mpesa_transaction_id' => $paymentResponse['transaction_id'] ?? null,
            'phone_number' => $mpesaContact,
            'amount' => $plan->price,
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->addDays($plan->duration_days),
        ]);
    }

    /**
     * Create a new subscription
     */
    public function subscribeWithMpesa(User $user, int|string $planId, string $mpesaContact): Subscription
    {
        $plan = $this->resolvePlan($planId);

        if ($user->hasActiveSubscription()) {
            throw new LendaException("O utilizador já possui uma subscrição ativa.", 400);
        }

        // Fix Reference (INS-21)
        $reference = 'S' . strtoupper(Str::random(7));

        $paymentResponse = $this->mpesaService->initiatePayment(
            phoneNumber: $mpesaContact,
            amount: $plan->price,
            reference: $reference
        );

        if (!$paymentResponse['success']) {
            throw new LendaException("Falha ao iniciar pagamento: " . ($paymentResponse['message'] ?? 'Erro'), 400);
        }

        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',          // <--- Active immediately
            'payment_status' => 'paid',
            'payment_method' => 'mpesa',
            'mpesa_reference' => $reference,
            'mpesa_transaction_id' => $paymentResponse['transaction_id'] ?? null,
            'phone_number' => $mpesaContact,
            'amount' => $plan->price,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($plan->duration_days),
        ]);
    }

    /**
     * Cancel without refund
     */
    public function cancelWithoutRefund(Subscription $subscription): void
    {
        if ($subscription->status === 'cancelled') {
            throw new LendaException("Esta subscrição já está cancelada.", 400);
        }
        $subscription->update(['status' => 'cancelled']);
    }

    /**
     * Upgrade Plan
     */
    public function upgradePlan(User $user, int $newPlanId, string $mpesaContact): Subscription
    {
        $newPlan = Plan::findOrFail($newPlanId);
        $currentSub = $user->currentSubscription();

        if ($currentSub && $currentSub->plan_id == $newPlanId) {
            throw new LendaException("O utilizador já está subscrito a este plano.", 400);
        }

        // Fix Reference (INS-21)
        $reference = 'U' . strtoupper(Str::random(7));
        
        $paymentResponse = $this->mpesaService->initiatePayment(
            phoneNumber: $mpesaContact,
            amount: $newPlan->price,
            reference: $reference
        );

        if (!$paymentResponse['success']) {
            throw new LendaException("Falha no pagamento: " . ($paymentResponse['message'] ?? 'Erro M-Pesa'), 400);
        }

        DB::beginTransaction();
        try {
            if ($currentSub) {
                $currentSub->update(['status' => 'upgraded', 'is_active' => false]);
            }

            $newSub = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $newPlan->id,
                'status' => 'active',       // <--- Active immediately
                'payment_status' => 'paid',
                'payment_method' => 'mpesa',
                'mpesa_reference' => $reference,
                'mpesa_transaction_id' => $paymentResponse['transaction_id'] ?? null,
                'phone_number' => $mpesaContact,
                'amount' => $newPlan->price,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($newPlan->duration_days),
            ]);

            DB::commit();
            return $newSub;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new LendaException("Erro ao processar atualização.", 500);
        }
    }

    public function getUserSubscriptionStats(User $user): array
    {
        $totalSpent = $user->subscriptions()->where('payment_status', 'paid')->sum('amount');
        $activeSub = $user->currentSubscription();

        return [
            'total_spent' => $totalSpent,
            'total_subscriptions' => $user->subscriptions()->count(),
            'current_plan' => $activeSub ? $activeSub->plan->title : 'Gratuito', // Adjusted 'name' to 'title' if needed
            'member_since' => $user->created_at->format('M Y'),
        ];
    }

    private function resolvePlan(int|string $identifier): Plan
    {
        if (is_numeric($identifier)) {
            return Plan::findOrFail($identifier);
        }
        return Plan::where('slug', $identifier)->firstOrFail();
    }
}