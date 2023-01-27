<?php

namespace App\Services;

require_once app_path('NonComposerDependencies/gbxdatafetcher.php');

use App\Exceptions\InvalidReplayException;
use App\Models\Record;
use GBXReplayFetcher;
use Illuminate\Support\Facades\Log;

class RecordReplayMatcher {

    private GBXReplayFetcher $gbxReplayFetcher;

    public function __construct() {
        $this->gbxReplayFetcher = new GBXReplayFetcher(true);
    }

    public function checkReplayMatchesRecord(string $replay, Record $record): bool {
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

        return $scoreOfReplay == $scoreOfRecord;
    }

}
