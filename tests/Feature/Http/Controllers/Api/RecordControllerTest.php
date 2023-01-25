<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\RecordReplayController;
use App\Models\Map;
use App\Models\Player;
use App\Models\Record;
use App\Services\ReplayFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestData;

class RecordControllerTest extends TestCase
{
    use RefreshDatabase;

    private ReplayFileService $replayFileService;

    public function setUp(): void {
        parent::setUp();

        $this->replayFileService = new ReplayFileService();
        $this->replayFileService->useFakeDiskAndClearIt();
    }

    public function testCreateWorks()
    {
        $player = Player::factory()->create();
        $map1 = Map::factory()->create();
        $recordToSubmit = [
            'player_login' => $player->login,
            'map_uid' => $map1->uid,
            'score' => 333,
        ];

        $response = $this->put('/api/v5/records', $recordToSubmit);

        $response->assertStatus(201);
        $response->assertJson($recordToSubmit);
        $response->assertJson(['replay_available' => false]);
        $response->assertJsonStructure(['id']);
    }

    public function testUpdateWorks()
    {
        $originalRecord = TestData::record()->create();

        $recordToSubmit = [
            'player_login' => $originalRecord->player->login,
            'map_uid' => $originalRecord->map->uid,
            'score' => $originalRecord->score + 333,
        ];

        $response = $this->put('/api/v5/records', $recordToSubmit);

        $response->assertStatus(200);
        $response->assertJson($recordToSubmit);
    }

    public function testRecordNotUpdatedIfScoreIsWorse()
    {
        $originalRecord = TestData::record()->create();

        $recordToSubmit = [
            'player_login' => $originalRecord->player->login,
            'map_uid' => $originalRecord->map->uid,
            'score' => $originalRecord->score - 1,
        ];

        $response = $this->put('/api/v5/records', $recordToSubmit);

        $response->assertStatus(200);
        $response->assertJson([ 'score' => $originalRecord->score ]);
    }

    public function testUpdateWithUnknownPlayer()
    {
        $map1 = Map::factory()->create();
        $recordToSubmit = [
            'player_login' => 'non-existing-player',
            'map_uid' => $map1->uid,
            'score' => 333,
        ];

        $response = $this->put('/api/v5/records', $recordToSubmit);

        $response->assertStatus(201);
        $response->assertJson($recordToSubmit);
        $response->assertJsonStructure(['id']);
    }

    public function testUpdateWithUnknownMap()
    {
        $player = Player::factory()->create();
        $recordToSubmit = [
            'player_login' => $player->login,
            'map_uid' => 'non-existing-player',
            'score' => 333,
        ];

        $response = $this->put('/api/v5/records', $recordToSubmit);

        $response->assertStatus(201);
        $response->assertJson($recordToSubmit);
        $response->assertJsonStructure(['id']);
    }

    public function testRecordOfBannedPlayerNotSaved() {

        $player = Player::factory()
            ->banned()
            ->create();
        $map1 = Map::factory()->create();
        $recordToSubmit = [
            'player_login' => $player->login,
            'map_uid' => $map1->uid,
            'score' => 333,
        ];

        $response = $this->put('/api/v5/records', $recordToSubmit);

        $response->assertForbidden();
        $response->assertJson([
            'message' => [
                'code' => 'BANNED_PLAYER',
            ]
        ]);
    }

    public function testReplayDeletedIfRecordUpdated() {
        $record = TestData::record()->create();
        $this->replayFileService->storeReplay(TestData::validReplayWithScore142(), $record);

        $updatedRecordToSubmit = [
            'player_login' => $record->player->login,
            'map_uid' => $record->map->uid,
            'score' => $record->score + 345,
        ];

        $this->put('/api/v5/records', $updatedRecordToSubmit);

        $this->assertFalse($this->replayFileService->replayExists($record));
    }

    public function testReplayAvailableSetFalseIfRecordUpdated() {
        $record = Record::factory()->withReplayAvailable()
            ->for(Player::factory())
            ->for(Map::factory())
            ->create();

        $updatedRecordToSubmit = [
            'player_login' => $record->player->login,
            'map_uid' => $record->map->uid,
            'score' => $record->score + 345,
        ];

        $this->put('/api/v5/records', $updatedRecordToSubmit);

        $record->refresh();
        $this->assertFalse($record->replay_available);
    }
}
