<?php

use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\RecordController;
use App\Http\Controllers\Api\MapsRecordsController;
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

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::prefix('v5')->group(function () {
    Route::put('records', [RecordController::class, 'updateOrCreate']);
    Route::get('maps/{uid}/records', [MapsRecordsController::class, 'index']);
    Route::put('players', [PlayerController::class, 'updateOrCreate']);
});
