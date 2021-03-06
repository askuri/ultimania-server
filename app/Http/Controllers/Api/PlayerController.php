<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PlayerNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PlayerController extends Controller
{
    public function show(string $playerLogin) {
        $player = Player::find($playerLogin);

        if ($player === null) {
            throw new PlayerNotFoundException();
        }

        return $player;
    }

    public function updateOrCreate(Request $request)
    {
        $requestData = $request->validate([
            'login'  => 'string|required',
            'nick' => 'string|required',
            'allow_replay_download' => 'boolean',
        ]);

        $attributesToUpdate = [
            'nick' => $requestData['nick'],
        ];

        if ($request->has('allow_replay_download')) {
            $attributesToUpdate['allow_replay_download'] = $requestData['allow_replay_download'];
        }

        return Player::updateOrCreate(
            ['login' => $requestData['login']],
            $attributesToUpdate
        );
    }
}
