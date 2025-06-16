<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;


/**
 * * Backend Web Routes
 */

Route::post('/game/create', [GameController::class, 'createGame']);

