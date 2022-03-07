<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class InvalidReplayException extends \Exception {

    public function render(): Response
    {
        return response([
            'error' => [
                'code' => 'INVALID_REPLAY',
                'message' => $this->getMessage()
            ]
        ], 400);
    }
}
