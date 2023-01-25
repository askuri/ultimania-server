<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class ReplayNotMatchingRecordException extends \Exception {

    public function render(): Response
    {
        return response([
            'error' => [
                'code' => 'REPLAY_NOT_MATCHING_RECORD',
                'message' => $this->getMessage()
            ]
        ], 400);
    }
}
