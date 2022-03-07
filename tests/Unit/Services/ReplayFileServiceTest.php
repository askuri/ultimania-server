<?php

namespace Tests\Unit\Services;

use App\Services\ReplayFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestData;

class ReplayFileServiceTest extends TestCase {

    use RefreshDatabase;

    private ReplayFileService $service;

    public function setUp(): void {
        parent::setUp();
        $this->service = new ReplayFileService();
        $this->service->useFakeDiskAndClearIt();
    }

    public function testStoreAndRetrieveReplay() {
        $record = TestData::record()->make();

        $this->service->storeReplay(TestData::VALID_REPLAY_CONTENT, $record);
        $retrievedReplay = $this->service->retrieveReplay($record);

        $this->assertEquals(TestData::VALID_REPLAY_CONTENT, $retrievedReplay);
    }

    public function testStoreReplayAndReplayExists() {
        $record = TestData::record()->make();

        $this->service->storeReplay(TestData::VALID_REPLAY_CONTENT, $record);

        $this->assertTrue($this->service->replayExists($record));
    }

    public function testStoreAndDeleteReplayIfExists() {
        $record = TestData::record()->make();

        $this->service->storeReplay(TestData::VALID_REPLAY_CONTENT, $record);
        $this->service->deleteReplayIfExists($record);

        $this->assertFalse($this->service->replayExists($record));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testDeleteReplayIfNotExists() {
        $record = TestData::record()->make();

        // if anything fails here, it will automatically fail the test. no assertion needed.
        $this->service->deleteReplayIfExists($record);
    }
}
