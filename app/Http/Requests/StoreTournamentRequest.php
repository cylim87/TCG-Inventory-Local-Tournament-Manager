<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'game' => 'required|in:pokemon,mtg,yugioh,one_piece,lorcana,fab,digimon,union_arena,other',
            'format' => 'required|in:standard,expanded,modern,legacy,pioneer,vintage,draft,sealed,commander,pre_release,limited,other',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'entry_fee' => 'nullable|numeric|min:0',
            'prize_pool' => 'nullable|numeric|min:0',
            'max_players' => 'nullable|integer|min:2|max:512',
            'rounds' => 'nullable|integer|min:1|max:15',
            'top_cut' => 'nullable|integer|in:0,2,4,8,16',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
