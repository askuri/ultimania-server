<?php

namespace App\Console\Commands;

use App\Services\ReplayFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshReplayAvailableFlag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh-replay-available';

    /**
     * The console command description.
     ** @var string
     */
    protected $description = 'Sets the replay_available flag on all records based on actually existent replays.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ReplayFileService $replayFileService)
    {
        $this->info("Collecting existent replay ids ...");
        $actuallyReplayRecordIds = $replayFileService->getAllReplayRecordIds();
        $actualReplaysCount = count($actuallyReplayRecordIds);
        $this->info("... done. Found $actualReplaysCount.");

        if ($actualReplaysCount == 0) {
            $this->error("No replays found");
            return 1;
        }

        $this->info("Resetting replay_available flags ...");
        DB::table('records')->update(['replay_available' => false]);
        $this->info("... done");

        foreach ($actuallyReplayRecordIds as $i => $recordId) {
            $this->info("Updating $recordId. Progress: " . ($i+1 / $actualReplaysCount) * 100 . "%");

            DB::table('records')
                ->where('id', '=', $recordId)
                ->update(['replay_available' => true]);
        }

        $this->info("done.");

        return 0;
    }
}
