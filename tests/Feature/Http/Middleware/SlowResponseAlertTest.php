<?php

namespace Tests\Feature\Http\Middleware;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

// LARAVEL_START is not defined during tests.
define('LARAVEL_START', microtime(true));

class SlowResponseAlertTest extends TestCase {

    public function testAlertOnSlowRequests() {
        Config::set('app.slow_response_alert_threshold', 0.0000000001);

        Log::listen(fn($log) => $this->assertStringContainsString('Ultimania server response time is critically long', $log->message));
        $this->get('');
    }
}
