<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'board',
        'result',
        'ships_sunk',
        'ships_lost',
        'last_seen_at',
        'ready',
    ];

    protected $casts = [
        'board' => 'array',
        'last_seen_at' => 'datetime',
        'ready' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function game() {
        return $this->belongsTo(Game::class);
    }
}
