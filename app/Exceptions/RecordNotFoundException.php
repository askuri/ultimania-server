<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class RecordNotFoundException extends \Exception {

    public function render(): Response
    {
        return response([
            'error' => [
                'code' => 'RECORD_NOT_FOUND',
                'message' => $this->getMessage()
            ]
        ], 404);
    }
}
