<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'player_number', 'date_of_birth', 'preferred_game', 'notes', 'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    public function registrations()
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function tournaments()
    {
        return $this->hasManyThrough(Tournament::class, TournamentRegistration::class, 'player_id', 'id', 'id', 'tournament_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('player_number', 'like', "%{$term}%");
        });
    }

    public function getTournamentStatsAttribute(): array
    {
        $registrations = $this->registrations()->with('tournament')->get();
        return [
            'total' => $registrations->count(),
            'completed' => $registrations->filter(fn($r) => $r->tournament->status === 'completed')->count(),
        ];
    }
}
