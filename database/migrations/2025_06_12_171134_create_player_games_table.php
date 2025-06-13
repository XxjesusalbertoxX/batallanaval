<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_games', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Jugador
            $table->foreignId('game_id')->constrained()->onDelete('cascade'); // Partida

            $table->json('board'); // Mapa 8x8 con 0, 1, 2, 3
            $table->enum('result', ['win', 'lose', 'pending'])->default('pending');
            $table->unsignedTinyInteger('ships_sunk')->default(0);
            $table->unsignedTinyInteger('ships_lost')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_games');
    }
};
