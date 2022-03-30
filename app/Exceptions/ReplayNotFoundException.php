<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class ReplayNotFoundException extends \Exception {

    public function render(): Response
    {
        return response([
            'error' => [
                'code' => 'REPLAY_NOT_FOUND',
                'message' => $this->getMessage()
            ]
        ], 404);
    }
}
