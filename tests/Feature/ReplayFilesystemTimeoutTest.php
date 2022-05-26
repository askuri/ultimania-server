<?php

namespace Tests\Feature;

use App\Services\ReplayFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\TestData;

class ReplayFilesystemTimeoutTest extends TestCase
{

    public function testReplaySftpFilesystemMaxTimeout()
    {
        Config::set('filesystems.disks.replays_sftp.host', 'askuri.de');
        Config::set('filesystems.disks.replays_sftp.port', 12345);
        Config::set('filesystems.disks.replays_sftp.timeout', 1);
        // maxTries should be 1 by config. The effectiveness of that setting is tested here

        $startTime = microtime(true);
        $endTime = $startTime + 60;
        try {
            Storage::disk('replays_sftp')->exists("irrelevant");
        } catch (UnableToCheckFileExistence $e) {
            $endTime = microtime(true);
        }

        $time = $endTime - $startTime;

        $this->assertLessThan(1.5, $time);
    }
}
