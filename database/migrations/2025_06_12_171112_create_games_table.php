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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['waiting', 'started', 'in_progres', 'finished'])->default('waiting');
            $table->unsignedBigInteger('current_turn_user_id')->nullable(); // ID del usuario cuyo turno es actualmente
            $table->foreign('current_turn_user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('code')->unique(); // por ejemplo, algo como 'ABC123'
            $table->boolean('has_started')->default(false);
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
        Schema::dropIfExists('games');
    }
};
