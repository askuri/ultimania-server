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
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);

        $threshold = config('app.slow_response_alert_threshold');
        $responseTime = $endTime - $startTime;
        if ($threshold > 0 && $responseTime > $threshold) {
            Log::alert("Ultimania server response time is critically long. This request took $responseTime seconds.");
        }

        return $response;
    }
}
