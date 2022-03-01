<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Map;
use App\Models\Player;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class MapController extends Controller
{
    public function updateOrCreate(Request $request)
    {
        $requestData = $request->validate([
            'uid'  => 'string|required',
            'name' => 'string|required',
        ]);

        return Map::updateOrCreate(
            ['uid' => $requestData['uid']],
            ['name' => $requestData['name']]
        );
    }
}
