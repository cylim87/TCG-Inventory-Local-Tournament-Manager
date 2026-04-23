<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'game', 'name', 'set_code', 'release_date', 'total_cards', 'series', 'is_active',
    ];

    protected $casts = [
        'release_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGame($query, string $game)
    {
        return $query->where('game', $game);
    }

    public static function gameLabel(string $game): string
    {
        return match($game) {
            'pokemon' => 'Pokémon',
            'mtg' => 'Magic: The Gathering',
            'yugioh' => 'Yu-Gi-Oh!',
            'one_piece' => 'One Piece',
            'lorcana' => 'Disney Lorcana',
            'fab' => 'Flesh and Blood',
            'digimon' => 'Digimon',
            'union_arena' => 'Union Arena',
            default => 'Other',
        };
    }
}
