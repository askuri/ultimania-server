<?php

namespace App\Services;

use App\Models\Record;
use Illuminate\Support\Facades\Storage;

class ReplayFileService {

    public function retrieveReplay(Record $forRecord): ?string {
        return Storage::disk('replays')->get($this->getFilename($forRecord));
    }

    /**
     * Check if a replay exists for the given record. Score of the record may differ.
     */
    public function replayExists(Record $forRecord): bool {
        return Storage::disk('replays')->exists($this->getFilename($forRecord));
    }

    public function storeReplay(string $replayContent, Record $forRecord): void {
        Storage::disk('replays')->put($this->getFilename($forRecord), $replayContent);
    }

    public function deleteReplayIfExists(Record $forRecord): void {
        Storage::disk('replays')->delete($this->getFilename($forRecord));
    }

    /**
     * Sets the storage disk to temporary disk and clear it.
     * Important: this disk will remain populated until the application shuts down
     * or this method is called again.
     */
    public function useFakeDiskAndClearIt(): void {
        Storage::fake('replays');
    }

    /**
     * Generate a replay filename for a record.
     * Calling this method twice on the semantically same record must return the same result.
     */
    private function getFilename(Record $forRecord): string {
        return $forRecord->id;
    }
}
