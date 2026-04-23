<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentRound extends Model
{
    protected $fillable = [
        'tournament_id', 'round_number', 'status', 'is_top_cut', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_top_cut' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function pairings()
    {
        return $this->hasMany(Pairing::class)->orderBy('table_number');
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->pairings()->where('result', 'pending')->count() === 0;
    }

    public function getPendingPairingsCountAttribute(): int
    {
        return $this->pairings()->where('result', 'pending')->count();
    }
}
