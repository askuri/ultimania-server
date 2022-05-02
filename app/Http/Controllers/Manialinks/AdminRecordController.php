<?php

namespace App\Http\Controllers\Manialinks;

header("Cache-Control: no-cache");

use App\Http\Controllers\Controller;
use App\Models\Record;
use Illuminate\Http\Request;

class AdminRecordController extends Controller {

    public function show(Request $request) {
        return view('manialinks.admin-rec.show', [
            'login' => $request->get('login', ''),
            'uid' => $request->get('uid'),
        ]);
    }

    public function deleteAndBan(Request $request) {
        $login = $request->get('login');
        $uid = $request->get('uid');
        $password = $request->get('password');

        if (empty($password)) {
            return view('manialinks.admin-rec.show', [
                'login' => $login,
                'uid' => $uid,
                'message' => '$d00Password cannot be empty.'
            ]);
        }

        if (! in_array($password, config('app.admin_rec_passwords'))) {
            return view('manialinks.admin-rec.show', [
                'login' => $login,
                'uid' => $uid,
                'message' => '$d00Wrong password.'
            ]);
        }

        $record = Record::where(['player_login' => $login, 'map_uid' => $uid])->first();

        if ($record == null) {
            return view('manialinks.admin-rec.show', [
                'login' => $login,
                'uid' => $uid,
                'message' => '$d00Record does not exist.'
            ]);
        }

        $player = $record->player;
        $player->banned = true;
        $player->save();

        $record->delete();

        return view('manialinks.admin-rec.show', [
            'login' => $login,
            'uid' => $uid,
            'message' => '$0f0Record successfully deleted and player banned.'
        ]);
    }
}
