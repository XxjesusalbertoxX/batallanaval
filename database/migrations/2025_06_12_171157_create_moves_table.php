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
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_game_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('x'); // Columna del tablero (0-7)
            $table->unsignedTinyInteger('y'); // Fila del tablero (0-7)
            $table->boolean('hit');           // Si fue impacto
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
        Schema::dropIfExists('moves');
    }
};
