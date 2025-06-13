<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'game_id' => Game::factory(),
            'player_name' => $this->faker->name(),
            'ships' => $this->faker->randomElements(range(0, 63), 16), // 16 barcos aleatorios en 8x8
        ];
    }
}
