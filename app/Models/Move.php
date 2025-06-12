<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    use HasFactory;

    protected $fillable = ['player_game_id', 'x', 'y', 'hit'];

    public function playerGame()
    {
        return $this->belongsTo(PlayerGame::class);
    }
}
