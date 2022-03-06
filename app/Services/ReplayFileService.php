<?php

namespace App\Services;

use App\Models\Record;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class ReplayFileService {

    private Filesystem $filesystem;

    private string $storage;

    public function __construct() {
        $this->storage = config('app.replays_filesystem');
        $this->filesystem = Storage::disk($this->storage);
    }

    public function retrieveReplay(Record $forRecord): ?string {
        return $this->filesystem->get($this->getFilename($forRecord));
    }

    /**
     * Check if a replay exists for the given record. Score of the record may differ.
     */
    public function replayExists(Record $forRecord): bool {
        return $this->filesystem->exists($this->getFilename($forRecord));
    }

    public function storeReplay(string $replayContent, Record $forRecord): void {
        $this->filesystem->put($this->getFilename($forRecord), $replayContent);
    }

    public function deleteReplayIfExists(Record $forRecord): void {
        $this->filesystem->delete($this->getFilename($forRecord));
    }

    /**
     * Sets the storage disk to temporary disk and clear it.
     * Important: this disk will remain populated until the application shuts down
     * or this method is called again.
     */
    public function useFakeDiskAndClearIt(): void {
        Storage::fake($this->storage);
    }

    /**
     * Generate a replay filename for a record.
     * Calling this method twice on the semantically same record must return the same result.
     */
    private function getFilename(Record $forRecord): string {
        return $forRecord->id;
    }
}
