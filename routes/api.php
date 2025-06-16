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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth')->group(function () {
    // Route::post('/game/create', [GameController::class, 'createGame']);
    Route::post('/game/{x}/{y}/attack', [GameController::class, 'attack']);
    Route::get('/game/{id}/heartbeat', [GameController::class, 'heartbeat']);
    Route::post('/game/{id}/leave', [GameController::class, 'surrender']);
    
    // Additional routes for game management
    Route::post('/games/{game}/actions', [GameController::class, 'action']);
});

Route::post('/game/join', [GameController::class, 'joinGame']);
    Route::get('/game/{id}/status', [GameController::class, 'checkGameStatus']);
