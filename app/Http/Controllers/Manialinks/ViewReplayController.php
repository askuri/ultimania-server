<?php

namespace App\Http\Controllers\Manialinks;

use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Services\ReplayFileService;
use Illuminate\Http\Request;

class ViewReplayController extends Controller {
    private ReplayFileService $replayFileService;

    /**
     * @param ReplayFileService $replayFileService
     */
    public function __construct(ReplayFileService $replayFileService) {
        $this->replayFileService = $replayFileService;
    }


    public function show(Request $request) {
        $recordId = $request->validate([
            'record_id' => 'required|string',
        ])['record_id'];

        $record = Record::find($recordId);

        if ($record === null) {
            // don't return HTTP Status 404 because the Manialink browser will ignore the body and just display its own "not found" message
            return view('manialinks.maniacode-message', ['message' => "Unfortunately we couldn't find the record you're trying to get a replay for."]);
        }
        if (!$record->getReplayAvailableAttribute()) {
            return view('manialinks.maniacode-message', ['message' => "We couldn't find a replay for this track or the player doesn't allow downloading it."]);
        }

        return view('manialinks.download-replay.show', [
            'url' => route('get_replay', $recordId)
        ]);
    }
}
