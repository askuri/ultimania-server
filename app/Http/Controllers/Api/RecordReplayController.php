<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidReplayException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReplayNotFoundException;
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
     * @throws RecordNotFoundException|InvalidReplayException
     */
    public function store(Request $request, string $recordId) {
        $replay = $request->getContent();

        try {
            $this->replayFileService->storeReplay($replay, Record::findOrFail($recordId));
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException();
        }

        return response('', 201);
    }

    /**
     * @throws RecordNotFoundException
     */
    public function show(string $recordId) {
        try {
            $record = Record::findOrFail($recordId);

            if (!$record->getReplayAvailableAttribute()) {
                throw new ReplayNotFoundException();
            }

            return $this->replayFileService->retrieveReplay($record);

        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException();
        }
    }
}
