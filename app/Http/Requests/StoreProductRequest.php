<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . ($this->product?->id ?? 'NULL'),
            'category' => 'required|in:booster_box,carton,booster_pack,single_card,elite_trainer_box,starter_deck,bundle,accessory,supply,other',
            'card_set_id' => 'nullable|exists:card_sets,id',
            'game' => 'nullable|in:pokemon,mtg,yugioh,one_piece,lorcana,fab,digimon,union_arena,other',
            'description' => 'nullable|string',
            'msrp' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'boxes_per_carton' => 'integer|min:1|max:100',
            'packs_per_box' => 'nullable|integer|min:1',
            'cards_per_pack' => 'nullable|integer|min:1',
            'barcode' => 'nullable|string|max:100',
            'image_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'reorder_point' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:1',
        ];
    }
}
