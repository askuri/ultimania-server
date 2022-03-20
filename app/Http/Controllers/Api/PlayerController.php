<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PlayerController extends Controller
{
    public function updateOrCreate(Request $request)
    {
        $requestData = $request->validate([
            'login'  => 'string|required',
            'nick' => 'string|required',
            'allow_replay_download' => 'boolean|required',
        ]);

        return Player::updateOrCreate(
            ['login' => $requestData['login']],
            ['nick' => $requestData['nick'], 'allow_replay_download' => $requestData['allow_replay_download']]
        );
    }
}
