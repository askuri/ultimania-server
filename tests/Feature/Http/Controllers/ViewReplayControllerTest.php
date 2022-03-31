<?php

namespace Tests\Feature\Http\Controllers;

use App\Services\ReplayFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\TestData;

class ViewReplayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccess()
    {
        $record = TestData::record()->create();
        $this->mock(ReplayFileService::class, function (MockInterface $mock) {
            $mock->shouldReceive('replayExists')->andReturn(true);
        });

        $getResponse = $this->get('manialinks/view_replay?record_id=' . $record->id);
        $getResponse->assertSee('view_replay');
        $getResponse->assertSee($record->id);
    }

    public function testNoRecord()
    {
        $getResponse = $this->get('manialinks/view_replay?record_id=nothing');
        $getResponse->assertOk();
        $getResponse->assertSee('record');
    }

    public function testNoReplay()
    {
        $record = TestData::record()->create();

        $getResponse = $this->get('manialinks/view_replay?record_id=' . $record->id);
        $getResponse->assertOk();
        $getResponse->assertSee('replay');
    }
}
