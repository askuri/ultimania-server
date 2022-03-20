<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testPlayerStoredIfNotExist()
    {
        $player = Player::factory()->make();
        $playerToSubmit = [
            'login' => $player->login,
            'nick' => $player->nick,
            'allow_replay_download' => $player->allow_replay_download,
        ];

        $response = $this->put('/api/v5/players', $playerToSubmit);

        $response->assertStatus(201);
        $response->assertJson($playerToSubmit);
    }

    public function testPlayerUpdatedIfExists()
    {
        $player = Player::factory()->create();
        $updatedPlayerToSubmit = [
            'login' => $player->login,
            'nick' => 'updated nickname',
            'allow_replay_download' => ! $player->allow_replay_download,
        ];

        $response = $this->put('/api/v5/players', $updatedPlayerToSubmit);

        $response->assertStatus(200);
        $response->assertJson($updatedPlayerToSubmit);
    }
}
