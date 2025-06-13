<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PlayerGame;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Move>
 */
class MoveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'player_game_id' => PlayerGame::factory(),
            'x' => $this->faker->numberBetween(0, 7),
            'y' => $this->faker->numberBetween(0, 7),
            'hit' => $this->faker->boolean(),
        ];
    }
}
