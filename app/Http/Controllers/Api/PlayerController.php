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
            'auto_upload_replay' => 'boolean|required',
        ]);

        return Player::updateOrCreate(
            ['login' => $requestData['login']],
            ['nick' => $requestData['nick'], 'id' => Uuid::uuid4()]
        );
    }
}
