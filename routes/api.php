<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\EbookController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\EpisodeController;
use App\Http\Controllers\Api\V1\PodcastController;
use App\Http\Controllers\Api\V1\AudiobookController;
use App\Http\Controllers\Api\V1\NewsletterController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\RegistrationController;
use App\Http\Controllers\Api\V1\SubscriptionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// --- PUBLIC ROUTES (No Auth) ---
Route::prefix('v1')->group(function () {
    // Pages
    Route::get('termos-e-condicoes', [PageController::class, 'termsAndConditions']);
    Route::get('politicas-de-privacidade', [PageController::class, 'privacyPolicy']);
    Route::get('sobre-nos', [PageController::class, 'aboutUs']);

    // Auth
    Route::post('/register-with-payment', [RegistrationController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

    // Public Content Access
    Route::get('user/access', [ContentController::class, 'getUserAccess']);
    Route::get('/ebook/all-books', [EbookController::class, 'getAllEbooks']);
    Route::get('/ebook/show/{slug}', [EbookController::class, 'showBySlug']);
    Route::get('/ebook/details/{slug}', [EbookController::class, 'showDetailsBySlug']);
    Route::get('/ebook-series/{slug}/ebooks', [EbookController::class, 'getBySeriesSlug']);
    Route::get('/ebook-series', [EbookController::class, 'getAllSeries']);
    Route::get('ebooks/latest-ebook', [EbookController::class, 'getLatestEbook']);
    
    Route::get('audiobooks/latest-audiobook', [AudiobookController::class, 'getLatestAudiobook']);
    Route::get('audiobook/{slug}', [AudiobookController::class, 'showBySlug']);
    Route::get('/audiobook-series/{slug}', [AudiobookController::class, 'getSeriesBySlug']);
    Route::get('/audiobook-series/{slug}/audiobooks', [AudiobookController::class, 'getBooksBySeriesSlug']);
    
    Route::get('newsletter/{slug}', [NewsletterController::class, 'showBySlug']);
    Route::get('latest-newsletter', [NewsletterController::class, 'getLatestNewsletter']);
    
    Route::get('/podcasts', [ContentController::class, 'getPodcasts']);
    Route::get('latest-episode', [PodcastController::class, 'getLatestPodcastEpisode']);
    Route::get('/podcasts/rumores-da-lenda', [PodcastController::class, 'getRumoresDaLenda']);
    Route::get('episodes-slider', [PodcastController::class, 'getEpisodesForSlider']);
    Route::get('episodes/{slug}', [EpisodeController::class, 'showBySlug']);

    // API Resources (Public Read)
    Route::apiResource('ebooks', EbookController::class)->only(['index', 'show']);
    Route::apiResource('audiobooks', AudiobookController::class)->only(['index', 'show']);
    Route::apiResource('newsletters', NewsletterController::class)->only(['index', 'show']);
    Route::apiResource('podcasts', PodcastController::class)->only(['index', 'show']);
    Route::apiResource('episodes', EpisodeController::class)->only(['index', 'show']);

    // Plans
    Route::get('plans', [\App\Http\Controllers\Api\V1\PlanController::class, 'index'])->name('api.plan.index');
    Route::get('plans/{slug}', [\App\Http\Controllers\Api\V1\PlanController::class, 'show'])->name('api.plan.show');
});


// --- PROTECTED ROUTES (Require Auth) ---
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Auth & Profile
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);

    // Progress
    Route::post('/progress/save', [ProgressController::class, 'saveProgress']);
    Route::get('/progress/get', [ProgressController::class, 'getProgress']);

    // Subscription Management (MOVED INSIDE v1)
    Route::prefix('subscriptions')->group(function () {
        Route::get('/current', [SubscriptionController::class, 'current']);
        Route::get('/history', [SubscriptionController::class, 'history']);
        Route::get('/stats', [SubscriptionController::class, 'stats']);
        
        Route::post('/', [SubscriptionController::class, 'subscribe']);
        Route::post('/renew', [SubscriptionController::class, 'renew']); // <--- NOW ACCESSIBLE AT api/v1/subscriptions/renew
        Route::post('/upgrade', [SubscriptionController::class, 'upgrade']);
        Route::post('/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
        
        Route::get('/check-access/{planSlug}', [SubscriptionController::class, 'checkAccess']);
    });

    // Email Verification
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    
    // Admin/Resource management (if needed)
    Route::post('plans', [\App\Http\Controllers\Api\V1\PlanController::class, 'store']);
    Route::put('plans/{plan}', [\App\Http\Controllers\Api\V1\PlanController::class, 'update']);
    Route::delete('plans/{plan}', [\App\Http\Controllers\Api\V1\PlanController::class, 'destroy']);
});