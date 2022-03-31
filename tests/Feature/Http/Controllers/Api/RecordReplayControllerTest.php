<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Exceptions\InvalidReplayException;
use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\Api\RecordReplayController;
use App\Models\Player;
use App\Services\ReplayFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Tests\TestData;

class RecordReplayControllerTest extends TestCase
{
    use RefreshDatabase;

    private RecordReplayController $controller;

    public function setUp(): void {
        parent::setUp();

        $replayFileService = new ReplayFileService();
        $replayFileService->useFakeDiskAndClearIt();
        $this->controller = new RecordReplayController($replayFileService);
    }

    public function testSavingAndRetrievingWorks()
    {
        $record = TestData::record()->create();
        $requestMock = $this->makePostRequestMock(TestData::VALID_REPLAY_CONTENT);

        $storeResponse = $this->controller->store($requestMock, $record->id);
        $this->assertTrue(json_decode($storeResponse->getContent())->replay_available);

        $getResponse = $this->get('api/v5/records/' . $record->id . '/replay');
        $getResponse->assertOk();
        $getResponse->assertSee(TestData::VALID_REPLAY_CONTENT);
    }

    public function testSavingFailsIfIdUnknown()
    {
        $requestMock = $this->makePostRequestMock(TestData::VALID_REPLAY_CONTENT);

        $this->expectException(RecordNotFoundException::class);
        $this->controller->store($requestMock, 'does_not_exist');
    }

    public function testRetrievingFailsIfIdUnknown()
    {
        $getResponse = $this->get('api/v5/records/does_not_exist/replay');
        $getResponse->assertNotFound();
        $getResponse->assertJson([
            'error' => [
                'code' => 'RECORD_NOT_FOUND',
            ]
        ]);
    }

    public function testRetrievingFailsIfReplayDownloadNotAllowed()
    {
        $record = TestData::record()
            ->for(Player::factory()->replay_download_forbidden()->create())
            ->create();
        $requestMock = $this->makePostRequestMock(TestData::VALID_REPLAY_CONTENT);
        $this->controller->store($requestMock, $record->id);

        $getResponse = $this->get('api/v5/records/' . $record->id . '/replay');

        $getResponse->assertNotFound();
        $getResponse->assertJson([
            'error' => [
                'code' => 'REPLAY_NOT_FOUND',
            ]
        ]);
    }

    public function testRetrievingFailsIfNoReplayAvailable()
    {
        $record = TestData::record()->create();

        $getResponse = $this->get('api/v5/records/' . $record->id . '/replay');

        $getResponse->assertNotFound();
        $getResponse->assertJson([
            'error' => [
                'code' => 'REPLAY_NOT_FOUND',
            ]
        ]);
    }

    public function testSavingNonReplayFileFails() {
        $record = TestData::record()->create();
        $requestMock = $this->makePostRequestMock(TestData::INVALID_REPLAY_CONTENT);

        $this->expectException(InvalidReplayException::class);
        $this->controller->store($requestMock, $record->id);
    }

    /**
     * Laravel test's post method don't allow sending binary content, only arrays.
     * Therefore, we have to test the controller directly.
     */
    private function makePostRequestMock(string $replayContent) {
        $requestMock = \Mockery::mock(Request::class);
        $requestMock->allows()->getContent()->andReturns($replayContent);
        return $requestMock;
    }
}
