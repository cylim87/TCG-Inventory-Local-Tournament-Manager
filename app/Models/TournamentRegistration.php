<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentRegistration extends Model
{
    protected $fillable = [
        'tournament_id', 'player_id', 'seed', 'paid', 'dropped',
        'drop_round', 'deck_name', 'notes',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'dropped' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function pairingsAsPlayer1()
    {
        return $this->hasMany(Pairing::class, 'player1_registration_id');
    }

    public function pairingsAsPlayer2()
    {
        return $this->hasMany(Pairing::class, 'player2_registration_id');
    }

    public function prizePayout()
    {
        return $this->hasOne(PrizePayout::class, 'player_registration_id');
    }

    public function getAllPairingsAttribute()
    {
        return $this->pairingsAsPlayer1->merge($this->pairingsAsPlayer2);
    }

    public function getMatchPointsAttribute(): int
    {
        $points = 0;
        foreach ($this->pairingsAsPlayer1 as $pairing) {
            $points += match($pairing->result) {
                'player1_win' => 3,
                'draw' => 1,
                'bye' => 2,
                default => 0,
            };
        }
        foreach ($this->pairingsAsPlayer2 as $pairing) {
            $points += match($pairing->result) {
                'player2_win' => 3,
                'draw' => 1,
                default => 0,
            };
        }
        return $points;
    }
}
