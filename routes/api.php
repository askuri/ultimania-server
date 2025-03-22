<?php

use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\RecordController;
use App\Http\Controllers\Api\MapsRecordsController;
use App\Http\Controllers\Api\RecordReplayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v5')->group(function () {
    Route::put('records', [RecordController::class, 'updateOrCreate']);
    Route::get('maps/{uid}/records', [MapsRecordsController::class, 'index']);
    Route::get('players/{login}', [PlayerController::class, 'show']);
    Route::put('players', [PlayerController::class, 'updateOrCreate']);
    Route::put('maps', [MapController::class, 'updateOrCreate']);
    Route::get('records/{id}/replay', [RecordReplayController::class, 'show'])->name('get_replay');
    Route::post('records/{id}/replay', [RecordReplayController::class, 'store']);
});
