<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $playerId = $this->player?->id ?? 'NULL';

        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => "nullable|email|max:255|unique:players,email,{$playerId}",
            'phone' => 'nullable|string|max:50',
            'player_number' => "nullable|string|max:20|unique:players,player_number,{$playerId}",
            'date_of_birth' => 'nullable|date|before:today',
            'preferred_game' => 'nullable|in:pokemon,mtg,yugioh,one_piece,lorcana,fab,digimon,union_arena,other',
            'notes' => 'nullable|string',
        ];
    }
}
