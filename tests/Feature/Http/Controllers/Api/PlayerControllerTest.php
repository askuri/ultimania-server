<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowPlayerWorks()
    {
        $player = Player::factory()->create();

        $response = $this->get('/api/v5/players/'.$player->login);
        $response->assertOk();
        $response->assertJson($player->toArray());
    }

    public function testShowPlayerFailsIfPlayerDoesntExist()
    {
        $response = $this->get('/api/v5/players/doesnt_exist');
        $response->assertNotFound();
        $response->assertJson([
            'error' => [
                'code' => 'PLAYER_NOT_FOUND'
            ]
        ]);
    }

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
