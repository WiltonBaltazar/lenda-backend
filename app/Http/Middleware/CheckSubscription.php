<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$plans  // We can pass plan slugs to the middleware
     */
    public function handle(Request $request, Closure $next, ...$plans): Response
    {
        // Get the authenticated user
        $user = $request->user();

        // If no user or user doesn't have the required plan
        if (!$user || !$user->hasPlan($plans)) {
            return response()->json([
                'message' => 'You do not have access to this content. Please upgrade your plan.'
            ], 403); // 403 Forbidden
        }

        return $next($request);
    }
}
