<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Map;
use App\Models\Player;
use App\Models\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapsRecordsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexResponseComplete() {

        $map = Map::factory()->has(
            Record::factory()->count(1)->for(
                Player::factory()
            )
        )->create();

        $response = $this->get('/api/v5/maps/'.$map->uid.'/records');

        $response->assertStatus(200);
        $response->assertJson([[
            'id' => $map->records[0]->id,
            //'player_login' => $map->records[0]->player_login,
            'player' => [
                'login' => $map->records[0]->player->login,
                'nick' => $map->records[0]->player->nick,
                'banned' => $map->records[0]->player->banned,
                'auto_upload_replay' => $map->records[0]->player->auto_upload_replay,
                'created_at' => $map->records[0]->player->getSerializedCreatedAt(),
                'updated_at' => $map->records[0]->player->getSerializedUpdatedAt(),
            ],
            'map_uid' => $map->records[0]->map_uid,
            'score' => $map->records[0]->score,
            'created_at' => $map->records[0]->getSerializedCreatedAt(),
            'updated_at' => $map->records[0]->getSerializedUpdatedAt(),
        ]]);
    }

    public function testIndexLimitWorks() {
        $map = Map::factory()->has(
            Record::factory()->count(3)->for(
                Player::factory()
            )
        )->create();

        $response = $this->get('/api/v5/maps/'.$map->uid.'/records?limit=2');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }
}
