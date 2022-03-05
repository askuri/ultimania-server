<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Services\ReplayFileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RecordReplayController extends Controller
{
    private ReplayFileService $replayFileService;

    public function __construct(ReplayFileService $replayFileService) {
        $this->replayFileService = $replayFileService;
    }

    /**
     * @throws RecordNotFoundException
     */
    public function store(Request $request, string $id) {
        $replay = $request->getContent(false);

        try {
            $this->replayFileService->storeReplay($replay, Record::findOrFail($id));
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException();
        }

        return response('', 201);
    }

    /**
     * @throws RecordNotFoundException
     */
    public function show(string $id) {
        try {
            return $this->replayFileService->retrieveReplay(Record::findOrFail($id));
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException();
        }
    }
}
