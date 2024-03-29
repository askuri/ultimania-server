<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidReplayException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReplayNotFoundException;
use App\Exceptions\ReplayNotMatchingRecordException;
use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Services\RecordReplayMatcher;
use App\Services\ReplayFileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Flysystem\FilesystemException;

class RecordReplayController extends Controller {
    private ReplayFileService $replayFileService;
    private RecordReplayMatcher $recordReplayMatcher;

    public function __construct(ReplayFileService $replayFileService, RecordReplayMatcher $recordReplayMatcher) {
        $this->replayFileService = $replayFileService;
        $this->recordReplayMatcher = $recordReplayMatcher;
    }

    /**
     * @throws RecordNotFoundException|InvalidReplayException|ReplayNotMatchingRecordException
     */
    public function store(Request $request, string $recordId) {
        $replay = $request->getContent();

        try {
            $record = Record::findOrFail($recordId);

            if (!$this->recordReplayMatcher->checkReplayMatchesRecord($replay, $record)) {
                throw new ReplayNotMatchingRecordException(
                    "The supplied replay does not match the record.");
            }

            $this->replayFileService->storeReplay($replay, $record);
            $record->replay_available = true;
            $record->save();
        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException();
        } catch (FilesystemException $e) {
            Log::error("Can't store replay", ['exception' => $e, 'recordId' => $recordId]);
            return response(['replay_available' => false], 201);
        }

        return response(['replay_available' => $record->isReplayPubliclyAvailable()], 201);
    }

    /**
     * @throws RecordNotFoundException
     */
    public function show(string $recordId) {
        try {
            $record = Record::findOrFail($recordId);

            if (!$this->replayFileService->replayExists($record)) {
                throw new ReplayNotFoundException();
            }

            return $this->replayFileService->retrieveReplay($record);

        } catch (ModelNotFoundException) {
            throw new RecordNotFoundException();
        }
    }
}
