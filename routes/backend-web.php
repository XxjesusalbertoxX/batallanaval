<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;


/**
 * * Backend Web Routes
 */
    Route::post('/game/{x}/{y}/attack', [GameController::class, 'attack']);
    Route::get('/game/{id}/heartbeat', [GameController::class, 'heartbeat']);
    Route::post('/game/{id}/leave', [GameController::class, 'surrender']);
    
    // Additional routes for game management
    Route::post('/games/{game}/actions', [GameController::class, 'action']);
    Route::post('/game/join', [GameController::class, 'joinGame']);
    Route::post('/game/create', [GameController::class, 'createGame']);
    Route::post('/game/{id}/ready', [GameController::class, 'setReady']);
    Route::get('/game/{id}/status', [GameController::class, 'checkGameStatus']);

