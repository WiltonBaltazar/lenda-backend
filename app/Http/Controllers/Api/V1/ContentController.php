<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
   public function __construct()
    {
        // $this->middleware('subscription:books')->only(['getBooks', 'readBook']);
        // $this->middleware('subscription:lendinhas')->only(['getLendinhas', 'playLendinhas']);
        // // Podcasts need no middleware - they're free
    }

    public function getPodcasts()
    {
        return response()->json([
            'data' => 'All podcasts - free access',
            'access_level' => 'free'
        ]);
    }

    public function getBooks()
    {
        return response()->json([
            'data' => 'Books content',
            'required_plans' => ['premium', 'premium-lendinhas']
        ]);
    }

    public function getLendinhas()
    {
        return response()->json([
            'data' => 'Lendinhas content', 
            'required_plans' => ['lendinhas', 'premium-lendinhas']
        ]);
    }

    public function getUserAccess(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'access' => $user ? $user->getContentAccess() : ['podcasts' => true, 'books' => false, 'lendinhas' => false],
            'current_plan' => $user?->currentPlan()?->slug,
            'subscription_status' => $user?->getSubscriptionStatus() ?? 'guest'
        ]);
    }
}
