<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['status',
    'code',
    'current_turn_user_id',];

    public function players()
    {
        return $this->hasMany(PlayerGame::class);
    }
}
