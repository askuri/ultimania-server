<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Map;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateOrCreateWorks()
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
        $response->assertJsonStructure(['id']);
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
        // todo
    }
}
