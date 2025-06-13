<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
    Route::post('/game/create', [GameController::class, 'createGame']);
    Route::post('/game/join', [GameController::class, 'joinGame']);
    Route::get('/game/{id}/status', [GameController::class, 'checkGameStatus']);
    Route::post('/game/{x}/{y}/attack', [GameController::class, 'attack']);