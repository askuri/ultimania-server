<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function updateOrCreate(Request $request, Record $record)
    {
        $requestData = $request->validate([
            'player_login'  => 'string|required',
            'map_uid'       => 'string|required',
            'score'         => 'integer|required|min:1',
        ]);

        return Record::updateOrCreate(
            ['player_login' => $requestData['player_login'], 'map_uid' => $requestData['map_uid']],
            ['score' => $requestData['score']]
        );
    }
}
