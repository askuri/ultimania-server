<?php

namespace App\Console\Commands;

use App\Services\ReplayFileService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOrphanReplays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-orphan-replays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete replays that don\'t have a corresponding record in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ReplayFileService $replayFileService)
    {
        $replayRecordIds = $replayFileService->getAllReplayRecordIds();

        $databaseRecordIds = collect(DB::table('records')->select('id')->get())
            ->pluck('id')
            ->all();

        $orphanedReplayIds = collect($replayRecordIds)
            ->filter(fn($replayRecordId) => ! in_array($replayRecordId, $databaseRecordIds))
            ->all();

        $orphanedReplayCount = count($orphanedReplayIds);
        $this->info('Found '. $orphanedReplayCount .' orphaned replays.');

        foreach ($orphanedReplayIds as $index => $id) {
            $replayFileService->deleteReplayByRecordIdIfExists($id);
            $this->info('Deleting replay for '.$id.', number '.$index + 1 .' out of '.$orphanedReplayCount.'.');
        }

        $this->info('Peak memory usage: ' . memory_get_peak_usage() / 1024 / 1024 . 'MB');

        return 0;
    }
}
