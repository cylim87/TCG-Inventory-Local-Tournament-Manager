<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrizePayout extends Model
{
    protected $fillable = [
        'tournament_id', 'player_registration_id', 'placement',
        'cash_amount', 'prize_description', 'paid_out',
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'paid_out' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function registration()
    {
        return $this->belongsTo(TournamentRegistration::class, 'player_registration_id');
    }
}
