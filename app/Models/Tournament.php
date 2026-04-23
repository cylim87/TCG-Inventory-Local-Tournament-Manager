<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'game', 'format', 'date', 'start_time', 'entry_fee', 'prize_pool',
        'max_players', 'rounds', 'top_cut', 'status', 'description', 'notes', 'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'entry_fee' => 'decimal:2',
        'prize_pool' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function activeRegistrations()
    {
        return $this->hasMany(TournamentRegistration::class)->where('dropped', false);
    }

    public function rounds()
    {
        return $this->hasMany(TournamentRound::class)->orderBy('round_number');
    }

    public function currentRound()
    {
        return $this->hasOne(TournamentRound::class)->where('status', 'active')->latest('round_number');
    }

    public function prizePayouts()
    {
        return $this->hasMany(PrizePayout::class);
    }

    public function getPlayerCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    public function getActivePlayerCountAttribute(): int
    {
        return $this->registrations()->where('dropped', false)->count();
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->registrations()->where('paid', true)->count() * $this->entry_fee;
    }

    public function getRecommendedRoundsAttribute(): int
    {
        $count = $this->active_player_count;
        if ($count <= 0) return 3;
        return match(true) {
            $count <= 4 => 2,
            $count <= 8 => 3,
            $count <= 16 => 4,
            $count <= 32 => 5,
            $count <= 64 => 6,
            $count <= 128 => 7,
            default => 8,
        };
    }

    public function getCurrentRoundNumberAttribute(): int
    {
        return $this->rounds()->max('round_number') ?? 0;
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['registration', 'active', 'top_cut']);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())->orderBy('date');
    }

    public static function gameLabel(string $game): string
    {
        return CardSet::gameLabel($game);
    }

    public static function formatLabel(string $format): string
    {
        return match($format) {
            'standard' => 'Standard',
            'expanded' => 'Expanded',
            'modern' => 'Modern',
            'legacy' => 'Legacy',
            'pioneer' => 'Pioneer',
            'vintage' => 'Vintage',
            'draft' => 'Booster Draft',
            'sealed' => 'Sealed Deck',
            'commander' => 'Commander',
            'pre_release' => 'Pre-Release',
            'limited' => 'Limited',
            default => 'Other',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'registration' => 'blue',
            'active' => 'green',
            'top_cut' => 'yellow',
            'completed' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
