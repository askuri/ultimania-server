<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Exceptions\InvalidReplayException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReplayNotMatchingRecordException;
use App\Http\Controllers\Api\RecordReplayController;
use App\Models\Player;
use App\Services\RecordReplayMatcher;
use App\Services\ReplayFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use League\Flysystem\UnableToWriteFile;
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
        $this->controller = new RecordReplayController($replayFileService, new RecordReplayMatcher());
    }

    public function testSavingAndRetrievingWorks()
    {
        $record = TestData::record()->create();
        $requestMock = $this->makePostRequestMock(TestData::validReplayWithScore142());

        $storeResponse = $this->controller->store($requestMock, $record->id);
        $this->assertTrue(json_decode($storeResponse->getContent())->replay_available);

        $getResponse = $this->get('api/v5/records/' . $record->id . '/replay');
        $getResponse->assertOk();
        // assertSee somehow fails although content is identical. Maybe because of binary?
        $this->assertEquals(TestData::validReplayWithScore142(), $getResponse->getContent());
    }

    public function testSavingFailsIfIdUnknown()
    {
        $requestMock = $this->makePostRequestMock(TestData::validReplayWithScore142());

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
        $record = TestData::record()->withReplayAvailable()
            ->for(Player::factory()->replay_download_forbidden()->create())
            ->create();

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

    public function testSavingNonReplayFileFails()
    {
        $record = TestData::record()->create();
        $requestMock = $this->makePostRequestMock(TestData::invalidReplay());

        $this->expectException(InvalidReplayException::class);
        $this->controller->store($requestMock, $record->id);
    }

    public function testSavingReplayWithNoneMatchingScoreFails()
    {
        $record = TestData::record()->create();
        $record->score = 99;
        $record->save();
        $requestMock = $this->makePostRequestMock(TestData::validReplayWithScore142());

        $this->expectException(ReplayNotMatchingRecordException::class);
        $this->controller->store($requestMock, $record->id);
    }

    public function testReplayAvailableFalseIfReplayNotSaved()
    {
        $replayFileService = \Mockery::mock(ReplayFileService::class);
        $replayFileService->shouldReceive('storeReplay')->withAnyArgs()->andThrow(new UnableToWriteFile);
        $controller = new RecordReplayController($replayFileService, new RecordReplayMatcher());

        $record = TestData::record()->create();
        $requestMock = $this->makePostRequestMock(TestData::validReplayWithScore142());

        $storeResponse = $controller->store($requestMock, $record->id);

        $this->assertFalse(json_decode($storeResponse->getContent())->replay_available);

    }

    public function testReplayAvailableFlagSetToTrue()
    {
        $record = TestData::record()->create();
        $requestMock = $this->makePostRequestMock(TestData::validReplayWithScore142());

        $this->controller->store($requestMock, $record->id);

        $record->refresh();
        $this->assertTrue($record->replay_available);
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
