<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pairing extends Model
{
    protected $fillable = [
        'tournament_round_id', 'table_number',
        'player1_registration_id', 'player2_registration_id',
        'player1_games_won', 'player2_games_won', 'draws',
        'result', 'is_intentional_draw', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_intentional_draw' => 'boolean',
    ];

    public function round()
    {
        return $this->belongsTo(TournamentRound::class, 'tournament_round_id');
    }

    public function player1Registration()
    {
        return $this->belongsTo(TournamentRegistration::class, 'player1_registration_id');
    }

    public function player2Registration()
    {
        return $this->belongsTo(TournamentRegistration::class, 'player2_registration_id');
    }

    public function isBye(): bool
    {
        return $this->player2_registration_id === null;
    }

    public function getWinnerRegistrationAttribute(): ?TournamentRegistration
    {
        return match($this->result) {
            'player1_win', 'bye' => $this->player1Registration,
            'player2_win' => $this->player2Registration,
            default => null,
        };
    }

    public function scopePending($query)
    {
        return $query->where('result', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('result', '!=', 'pending');
    }
}
