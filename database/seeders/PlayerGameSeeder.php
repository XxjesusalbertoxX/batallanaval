<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Game::all()->each(function ($game) {
            PlayerGame::factory()->create(['game_id' => $game->id]);
            PlayerGame::factory()->create(['game_id' => $game->id]);
        });
    }
}
