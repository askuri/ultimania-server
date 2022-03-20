<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BannedPlayerException;
use App\Http\Controllers\Controller;
use App\Models\Map;
use App\Models\Player;
use App\Models\Record;
use App\Services\ReplayFileService;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class RecordController extends Controller
{

    private ReplayFileService $replayFileService;

    public function __construct(ReplayFileService $replayFileService) {
        $this->replayFileService = $replayFileService;
    }

    /**
     * @throws BannedPlayerException
     */
    public function updateOrCreate(Request $request)
    {
        $request->wantsJson();
        $newRecord = $this->validateRecordSubmissionAndReturnIt($request);

        // fallback in case for some reason the player or map was previously not created (should not occur)
        $player = $this->ensurePlayerExists($newRecord['player_login']);
        $this->ensureMapExists($newRecord['map_uid']);

        $this->bailOutIfPlayerIsBanned($player);

        $recordModel = $this->createRecordOrReturnExistingOne($newRecord);

        if ($newRecord['score'] > $recordModel->score) {
            $recordModel->score = $newRecord['score'];
            $recordModel->save();

            $this->replayFileService->deleteReplayIfExists($recordModel);
        }

        return $recordModel;
    }

    private function ensurePlayerExists($player_login) {
        return Player::firstOrCreate(
            ['login' => $player_login],
            (new Player(['login' => $player_login]))->toArray()
        );
    }

    private function ensureMapExists($map_uid): void {
        Map::firstOrCreate(
            ['uid' => $map_uid],
            (new Map(['uid' => $map_uid]))->toArray()
        );
    }

    private function validateRecordSubmissionAndReturnIt(Request $request): array {
        return $request->validate([
            'player_login' => 'string|required',
            'map_uid' => 'string|required',
            'score' => 'integer|required|min:1',
        ]);
    }

    /**
     * @throws BannedPlayerException
     */
    private function bailOutIfPlayerIsBanned($player): void {
        if ($player->banned === true) {
            throw new BannedPlayerException();
        }
    }

    private function createRecordOrReturnExistingOne(array $newRecord) {
        return Record::firstOrCreate(
            ['player_login' => $newRecord['player_login'], 'map_uid' => $newRecord['map_uid']],
            ['score' => $newRecord['score'], 'id' => Uuid::uuid4()]
        );
    }
}
