<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $subscription = $user?->subscription;

        if (! $subscription) {
            return redirect('/onboarding')->with('error', 'Please complete your subscription setup first.');
        }

        if ($subscription->expired_at && $subscription->expired_at->isPast()) {
            return redirect('/billing')->with('error', 'Your trial has expired. Please upgrade to continue.');
        }

        return $next($request);
    }
}
