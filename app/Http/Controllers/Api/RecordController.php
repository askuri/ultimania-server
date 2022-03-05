<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BannedPlayerException;
use App\Http\Controllers\Controller;
use App\Models\Map;
use App\Models\Player;
use App\Models\Record;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class RecordController extends Controller
{
    /**
     * @throws BannedPlayerException
     */
    public function updateOrCreate(Request $request)
    {
        $requestData = $request->validate([
            'player_login'  => 'string|required',
            'map_uid'       => 'string|required',
            'score'         => 'integer|required|min:1',
        ]);

        // fallback in case for some reason the player was previously not created (should not occur)
        $player = Player::firstOrCreate(
            ['login' => $requestData['player_login']],
            (new Player(['login' => $requestData['player_login']]))->toArray()
        );
        Map::firstOrCreate(
            ['uid' => $requestData['map_uid']],
            (new Map(['uid' => $requestData['map_uid']]))->toArray()
        );

        if ($player->banned === true) {
            throw new BannedPlayerException();
        }

        return Record::updateOrCreate(
            ['player_login' => $requestData['player_login'], 'map_uid' => $requestData['map_uid']],
            ['score' => $requestData['score'], 'id' => Uuid::uuid4()] // todo id is kept currently
        );
    }
}
