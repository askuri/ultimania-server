<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class BannedPlayerException extends \Exception {

    public function render(): Response
    {
        return response([
            'error' => [
                'code' => 'BANNED_PLAYER',
                'message' => $this->getMessage()
            ]
        ], 403);
    }
}
