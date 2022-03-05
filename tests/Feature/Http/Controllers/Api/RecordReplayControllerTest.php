<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\Api\RecordReplayController;
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
        $requestMock = $this->makePostRequestMock();

        $this->controller->store($requestMock, $record->id);

        $getResponse = $this->get('api/v5/records/' . $record->id . '/replay');
        $getResponse->assertOk();
        $getResponse->assertSee(TestData::REPLAY_CONTENT);
    }

    public function testSavingFailsIfIdUnknown()
    {
        $requestMock = $this->makePostRequestMock();

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

    public function testSavingNonReplayFileFails() {
        // todo
    }

    /**
     * Laravel test's post method don't allow sending binary content, only arrays.
     * Therefore, we have to test the controller directly.
     */
    private function makePostRequestMock() {
        $requestMock = \Mockery::mock(Request::class);
        $requestMock->allows()->getContent(false)->andReturns(TestData::REPLAY_CONTENT);
        return $requestMock;
    }
}
