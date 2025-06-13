<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Game;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlayerGame>
 */
class PlayerGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
            'board' => json_encode($this->generateRandomBoard(15)),
            'result' => 'pending',
            'ships_sunk' => 0,
            'ships_lost' => 0,
        ];
    }
}
