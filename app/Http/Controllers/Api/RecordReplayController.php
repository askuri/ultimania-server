<?php

namespace App\Http\Controllers\Api;

require_once app_path('includes/gbxdatafetcher.php');

use App\Exceptions\InvalidReplayException;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\ReplayNotFoundException;
use App\Exceptions\ReplayNotMatchingRecordException;
use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Services\ReplayFileService;
use GBXReplayFetcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Flysystem\FilesystemException;

class RecordReplayController extends Controller {
    private ReplayFileService $replayFileService;

    private GBXReplayFetcher $gbxReplayFetcher;

    public function __construct(ReplayFileService $replayFileService) {
        $this->replayFileService = $replayFileService;
        $this->gbxReplayFetcher = new GBXReplayFetcher(true);
    }

    /**
     * @throws RecordNotFoundException|InvalidReplayException|ReplayNotMatchingRecordException
     */
    public function store(Request $request, string $recordId) {
        $replay = $request->getContent();

        try {
            $record = Record::findOrFail($recordId);

            $this->checkReplayMatchesRecord($replay, $record);

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

    private function checkReplayMatchesRecord(string $replay, Record $record): void {
        $scoreOfRecord = $record->score;

        try {
            $this->gbxReplayFetcher->processData($replay);
            $scoreOfReplay = $this->gbxReplayFetcher->stuntScore;
        } catch (\Exception $e) {
            Log::error("Exception occurred while determining if replay matches record from database. Rejecting Replay.", [
                "record" => $record,
                "exception" => $e,
            ]);
            throw new InvalidReplayException("An exception occurred while processing the submitted replay. It's probably invalid.");
        }

        if ($scoreOfReplay != $scoreOfRecord) {
            throw new ReplayNotMatchingRecordException(
                "The supplied replay does not match the record. Score of replay: $scoreOfReplay; scoreOfRecord: $scoreOfRecord");
        }
    }
}
