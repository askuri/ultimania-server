<?php

namespace App\Http\Controllers\Manialinks;

use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Services\ReplayFileService;
use Illuminate\Http\Request;

class ViewReplayController extends Controller
{
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
            return response(
                view('manialinks.maniacode-message', ['message' => "Unfortunately we couldn't find the record you're trying to get a replay for."]),
                404
            );
        }
        if (!$this->replayFileService->replayExists($record)) {
            return response(
                view('manialinks.maniacode-message', ['message' => "Unfortunately we couldn't find a replay for this track."]),
                404
            );
        }

        return view('manialinks.download-replay.show', [
            'url' => route('get_replay', $recordId)
        ]);
    }
}
