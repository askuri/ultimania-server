<?php

use App\Http\Controllers\Manialinks\ViewReplayController;
use App\Http\Controllers\OpenApiController;
use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/swagger', [SwaggerController::class, 'show']);
Route::get('/openapi', [OpenApiController::class, 'show']);
Route::get('/manialinks/view_replay', [ViewReplayController::class, 'show']);
