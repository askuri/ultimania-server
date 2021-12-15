<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Map;
use Illuminate\Http\Request;

class MapsRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $uid, Request $request)
    {
        $validated = $request->validate([
            'limit' => 'integer|min:1',
        ]);

        return response(
            Map::find($uid)->records()
                ->orderByDesc('score')
                ->limit($validated['limit'] ?? 10000)
                ->with('player')
                ->get()
        );
    }
}
