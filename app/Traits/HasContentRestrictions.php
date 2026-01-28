<?php

namespace App\Traits;


trait HasContentRestrictions {
    public function canAccessBooks(): bool
    {
        return $this->hasActivePlan('premium') || $this->hasActivePlan('premium-lendinhas');
    }

    public function canAccessLendinhas(): bool {
        return $this->hasActivePlan('lendinhas') || $this->hasActivePlan('premium-lendinhas');
    }

    public function canAccessAllContent(): bool
    {
        return $this->hasActivePlan('premium-lendinhas');
    }

    public function getContentAccess(): array
    {
        $plan = $this->currentPlan();

        if(!$plan){
            return [
                'books' => false,
                'lendinhas' => false,
                'podcasts' => true, // Always free
            ];
        }

        return match($plan->slug){
            'premium' => [
                'books' => true,
                'lendinhas' => false,
                'podcasts' => true,
            ],
            'lendinhas' => [
                'books' => false,
                'lendinhas' => true,
                'podcasts' => true,
            ],
            'premium-lendinhas' => [
                'books' => true,
                'lendinhas' => true,
                'podcasts' => true,
            ],
            default => [
                'books' => false,
                'lendinhas' => false,
                'podcasts' => true, // Always free
            ]
        };
    }
}
