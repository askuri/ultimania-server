<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class PlayerNotFoundException extends \Exception {

    public function render(): Response
    {
        return response([
            'error' => [
                'code' => 'PLAYER_NOT_FOUND',
                'message' => $this->getMessage()
            ]
        ], 404);
    }
}
