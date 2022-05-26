<?php

namespace App\Services;

use App\Exceptions\InvalidReplayException;
use App\Models\Record;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;

class ReplayFileService {

    private string $storage;

    public function __construct() {
        $this->storage = config('app.replays_filesystem');
    }

    public function retrieveReplay(Record $forRecord): ?string {
        return $this->filesystem()->get($this->getFilename($forRecord));
    }

    /**
     * Check if a replay exists for the given record. Score of the record may differ.
     */
    public function replayExists(Record $forRecord): bool {
        return $this->filesystem()->exists($this->getFilename($forRecord));
    }

    /**
     * @throws InvalidReplayException
     * @throws FilesystemException
     */
    public function storeReplay(string $replayContent, Record $forRecord): void {
        if (!$this->isReplayValid($replayContent)) {
            if (!empty($replayContent)) {
                Log::alert("Someone tried to store a replay that is invalid and not empty!", ['payloadFirstKbBase64' => base64_encode(substr($replayContent, 0, 1024))]);
            }
            throw new InvalidReplayException('The replay you submitted seems to be invalid');
        }

        $this->filesystem()->put($this->getFilename($forRecord), $replayContent, );
    }

    /**
     * @throws FilesystemException
     */
    public function deleteReplayIfExists(Record $forRecord): void {
        $this->filesystem()->delete($this->getFilename($forRecord));
    }

    public function deleteReplayByRecordIdIfExists(string $id) {
        if ($this->filesystem()->exists($id)) {
            $this->filesystem()->delete($id);
        }
    }

    public function getAllReplayRecordIds() {
        return $this->filesystem()->allFiles();
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

    private function isReplayValid(string $replay): bool {
        return str_starts_with($replay, 'GBX');
    }

    private function filesystem(): Filesystem {
        return Storage::disk($this->storage);
    }
}
