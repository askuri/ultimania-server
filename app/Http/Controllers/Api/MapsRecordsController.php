<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MapNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Map;
use Illuminate\Http\Request;

class MapsRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws MapNotFoundException
     */
    public function index(string $uid, Request $request)
    {
        $validated = $request->validate([
            'limit' => 'integer|min:1',
        ]);

        $map = Map::find($uid);

        if ($map === null) {
            throw new MapNotFoundException();
        }

        return response($map->records()
                ->orderByDesc('score')
                ->limit($validated['limit'] ?? 10000)
                ->with('player')
                ->get()
        );
    }
}
