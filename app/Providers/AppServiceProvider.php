<?php

namespace App\Providers;

use App\Services\MpesaService;
use App\Services\SubscriptionService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(MpesaService::class);
        $this->app->singleton(MpesaService::class);
        $this->app->singleton(SubscriptionService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom Reset Password URL
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            // Ensure this points to your React App URL
            // You can use config('app.frontend_url') or hardcode it for testing
            $frontendUrl = config('app.frontend_url', 'http://localhost:3000'); 
            
            return "{$frontendUrl}/reset-password?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
