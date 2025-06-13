<?php

namespace App\Services;

use App\Models\Game;
use App\Models\PlayerGame;
use App\Traits\GeneratesBoard;
use Illuminate\Support\Facades\DB;

class GameService
{
    use GeneratesBoard;

    public function startGame(int $user1Id, int $user2Id): Game
    {
        return DB::transaction(function () use ($user1Id, $user2Id) {

            // 1. Crear la partida
            $game = Game::create([
                'status' => 'started',
                'current_turn_user_id' => $user1Id,
            ]);

            // 2. Crear tablero para user1
            PlayerGame::create([
                'user_id' => $user1Id,
                'game_id' => $game->id,
                'board' => json_encode($this->generateRandomBoard(15)),
                'result' => 'pending',
                'ships_sunk' => 0,
                'ships_lost' => 0,
            ]);

            // 3. Crear tablero para user2
            PlayerGame::create([
                'user_id' => $user2Id,
                'game_id' => $game->id,
                'board' => json_encode($this->generateRandomBoard(15)),
                'result' => 'pending',
                'ships_sunk' => 0,
                'ships_lost' => 0,
            ]);

            return $game;
        });
    }

    
}
