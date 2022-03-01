<?php

namespace Tests\Feature\Http\Controllers;

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
            'auto_upload_replay' => $player->auto_upload_replay,
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
            'auto_upload_replay' => ! $player->auto_upload_replay,
        ];

        $response = $this->put('/api/v5/players', $updatedPlayerToSubmit);

        $response->assertStatus(200);
        $response->assertJson($updatedPlayerToSubmit);
    }
}
