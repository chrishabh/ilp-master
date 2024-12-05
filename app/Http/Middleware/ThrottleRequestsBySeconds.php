<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequestsBySeconds
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts, $decaySeconds)
    {

        $key = 'throttle:' . $request->ip();

        // Check the current count
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return response()->json([
                'message' => 'Too Many Attempts. Please try again later.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        // Increment the count and set expiration time
        Cache::put($key, $attempts + 1, now()->addSeconds($decaySeconds));

        return $next($request);
    }
    
}
