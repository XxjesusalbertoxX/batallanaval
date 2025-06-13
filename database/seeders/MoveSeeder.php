<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MoveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PlayerGame::all()->each(function ($player) {
            Move::factory(5)->create(['player_game_id' => $player->id]);
        });
    }
}
