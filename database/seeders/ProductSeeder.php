<?php

namespace Database\Seeders;

use App\Models\CardSet;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Pokemon - Booster Boxes
            [
                'name' => 'Scarlet & Violet Booster Box',
                'sku' => 'PKM-SVI-BOX',
                'category' => 'booster_box',
                'game' => 'pokemon',
                'set_code' => 'SVI',
                'msrp' => 144.99,
                'cost_price' => 82.00,
                'boxes_per_carton' => 6,
                'packs_per_box' => 36,
                'cards_per_pack' => 10,
                'stock' => 12,
                'reorder_point' => 6,
            ],
            [
                'name' => 'Paradox Rift Booster Box',
                'sku' => 'PKM-PAR-BOX',
                'category' => 'booster_box',
                'game' => 'pokemon',
                'set_code' => 'PAR',
                'msrp' => 144.99,
                'cost_price' => 80.00,
                'boxes_per_carton' => 6,
                'packs_per_box' => 36,
                'cards_per_pack' => 10,
                'stock' => 8,
                'reorder_point' => 6,
            ],
            [
                'name' => 'Surging Sparks Booster Box',
                'sku' => 'PKM-SSP-BOX',
                'category' => 'booster_box',
                'game' => 'pokemon',
                'set_code' => 'SSP',
                'msrp' => 149.99,
                'cost_price' => 85.00,
                'boxes_per_carton' => 6,
                'packs_per_box' => 36,
                'cards_per_pack' => 10,
                'stock' => 18,
                'reorder_point' => 6,
            ],
            [
                'name' => 'Twilight Masquerade Booster Box',
                'sku' => 'PKM-TWM-BOX',
                'category' => 'booster_box',
                'game' => 'pokemon',
                'set_code' => 'TWM',
                'msrp' => 144.99,
                'cost_price' => 80.00,
                'boxes_per_carton' => 6,
                'packs_per_box' => 36,
                'cards_per_pack' => 10,
                'stock' => 4,
                'reorder_point' => 6,
            ],
            // Pokemon - ETBs
            [
                'name' => 'Surging Sparks Elite Trainer Box',
                'sku' => 'PKM-SSP-ETB',
                'category' => 'elite_trainer_box',
                'game' => 'pokemon',
                'set_code' => 'SSP',
                'msrp' => 49.99,
                'cost_price' => 28.00,
                'boxes_per_carton' => 8,
                'packs_per_box' => 9,
                'stock' => 24,
                'reorder_point' => 8,
            ],
            [
                'name' => 'Twilight Masquerade Elite Trainer Box',
                'sku' => 'PKM-TWM-ETB',
                'category' => 'elite_trainer_box',
                'game' => 'pokemon',
                'set_code' => 'TWM',
                'msrp' => 49.99,
                'cost_price' => 27.50,
                'boxes_per_carton' => 8,
                'packs_per_box' => 9,
                'stock' => 16,
                'reorder_point' => 8,
            ],
            // MTG - Booster Boxes
            [
                'name' => 'Bloomburrow Play Booster Box',
                'sku' => 'MTG-BLB-BOX',
                'category' => 'booster_box',
                'game' => 'mtg',
                'set_code' => 'BLB',
                'msrp' => 149.99,
                'cost_price' => 88.00,
                'boxes_per_carton' => 6,
                'packs_per_box' => 36,
                'cards_per_pack' => 14,
                'stock' => 6,
                'reorder_point' => 3,
            ],
            [
                'name' => 'Duskmourn Play Booster Box',
                'sku' => 'MTG-DSK-BOX',
                'category' => 'booster_box',
                'game' => 'mtg',
                'set_code' => 'DSK',
                'msrp' => 149.99,
                'cost_price' => 88.00,
                'boxes_per_carton' => 6,
                'packs_per_box' => 36,
                'cards_per_pack' => 14,
                'stock' => 10,
                'reorder_point' => 3,
            ],
            [
                'name' => 'Foundations Starter Collection',
                'sku' => 'MTG-FDN-BOX',
                'category' => 'booster_box',
                'game' => 'mtg',
                'set_code' => 'FDN',
                'msrp' => 169.99,
                'cost_price' => 98.00,
                'boxes_per_carton' => 6,
                'packs_per_box' => 36,
                'cards_per_pack' => 14,
                'stock' => 12,
                'reorder_point' => 6,
            ],
            // Yu-Gi-Oh
            [
                'name' => 'Phantom Nightmare Booster Box',
                'sku' => 'YGO-PHNI-BOX',
                'category' => 'booster_box',
                'game' => 'yugioh',
                'set_code' => 'PHNI',
                'msrp' => 95.76,
                'cost_price' => 55.00,
                'boxes_per_carton' => 12,
                'packs_per_box' => 24,
                'cards_per_pack' => 9,
                'stock' => 8,
                'reorder_point' => 4,
            ],
            [
                'name' => 'Infinite Forbidden Booster Box',
                'sku' => 'YGO-INFI-BOX',
                'category' => 'booster_box',
                'game' => 'yugioh',
                'set_code' => 'INFI',
                'msrp' => 95.76,
                'cost_price' => 54.00,
                'boxes_per_carton' => 12,
                'packs_per_box' => 24,
                'cards_per_pack' => 9,
                'stock' => 5,
                'reorder_point' => 4,
            ],
            // One Piece
            [
                'name' => 'One Piece OP06 Booster Box',
                'sku' => 'OP-OP06-BOX',
                'category' => 'booster_box',
                'game' => 'one_piece',
                'set_code' => 'OP06',
                'msrp' => 95.00,
                'cost_price' => 58.00,
                'boxes_per_carton' => 12,
                'packs_per_box' => 24,
                'cards_per_pack' => 12,
                'stock' => 3,
                'reorder_point' => 4,
            ],
            [
                'name' => 'One Piece OP09 Booster Box',
                'sku' => 'OP-OP09-BOX',
                'category' => 'booster_box',
                'game' => 'one_piece',
                'set_code' => 'OP09',
                'msrp' => 95.00,
                'cost_price' => 60.00,
                'boxes_per_carton' => 12,
                'packs_per_box' => 24,
                'cards_per_pack' => 12,
                'stock' => 24,
                'reorder_point' => 6,
            ],
            // Lorcana
            [
                'name' => "Lorcana Shimmering Skies Booster Box",
                'sku' => 'LOR-SSH-BOX',
                'category' => 'booster_box',
                'game' => 'lorcana',
                'set_code' => 'SSH',
                'msrp' => 144.00,
                'cost_price' => 85.00,
                'boxes_per_carton' => 4,
                'packs_per_box' => 24,
                'cards_per_pack' => 12,
                'stock' => 4,
                'reorder_point' => 4,
            ],
            // Accessories
            [
                'name' => 'Ultra Pro 9-Pocket Binder (Black)',
                'sku' => 'ACC-UP-BINDER-BLK',
                'category' => 'accessory',
                'game' => null,
                'set_code' => null,
                'msrp' => 19.99,
                'cost_price' => 9.50,
                'boxes_per_carton' => 12,
                'stock' => 30,
                'reorder_point' => 10,
            ],
            [
                'name' => 'Dragon Shield Matte Sleeves (100ct)',
                'sku' => 'ACC-DS-MATTE-100',
                'category' => 'accessory',
                'game' => null,
                'set_code' => null,
                'msrp' => 13.99,
                'cost_price' => 6.50,
                'boxes_per_carton' => 20,
                'stock' => 60,
                'reorder_point' => 20,
            ],
            [
                'name' => 'Gamegenic Watchtower 100+ Deck Box',
                'sku' => 'ACC-GG-WATCH-100',
                'category' => 'accessory',
                'game' => null,
                'set_code' => null,
                'msrp' => 19.99,
                'cost_price' => 10.00,
                'boxes_per_carton' => 10,
                'stock' => 15,
                'reorder_point' => 5,
            ],
            [
                'name' => 'Play Mat (Generic TCG)',
                'sku' => 'ACC-PLAYMAT-GEN',
                'category' => 'accessory',
                'game' => null,
                'set_code' => null,
                'msrp' => 24.99,
                'cost_price' => 11.00,
                'boxes_per_carton' => 10,
                'stock' => 12,
                'reorder_point' => 5,
            ],
        ];

        foreach ($products as $data) {
            $setCode = $data['set_code'] ?? null;
            $game = $data['game'] ?? null;
            $cardSetId = null;

            if ($setCode && $game) {
                $cardSetId = CardSet::where('set_code', $setCode)->where('game', $game)->value('id');
            }

            $stock = $data['stock'];
            $reorderPoint = $data['reorder_point'];
            unset($data['stock'], $data['reorder_point'], $data['set_code']);

            $product = Product::firstOrCreate(
                ['sku' => $data['sku']],
                array_merge($data, ['card_set_id' => $cardSetId])
            );

            InventoryItem::firstOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity_on_hand' => $stock,
                    'quantity_reserved' => 0,
                    'reorder_point' => $reorderPoint,
                    'reorder_quantity' => $reorderPoint * 2,
                    'average_cost' => $product->cost_price,
                ]
            );
        }

        $this->command->info('Products and inventory seeded (' . count($products) . ' products).');
    }
}
