<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerGame extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'player_name', 'ships'];

    protected $casts = [
        'ships' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }
}
