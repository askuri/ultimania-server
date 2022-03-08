<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class MapNotFoundException extends \Exception {

    public function render(): Response
    {
        return response([
            'error' => [
                'code' => 'MAP_NOT_FOUND',
                'message' => $this->getMessage()
            ]
        ], 404);
    }
}
