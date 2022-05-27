<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SlowResponseAlert
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response) {
        $responseTime = microtime(true) - \LARAVEL_START;

        $threshold = config('app.slow_response_alert_threshold');

        if ($threshold > 0 && $responseTime > $threshold) {
            Log::alert("Ultimania server response time is critically long. This request took $responseTime seconds.", ['request' => $request]);
        }

        return $response;
    }
}
