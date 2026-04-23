<?php

namespace Database\Seeders;

use App\Models\CardSet;
use Illuminate\Database\Seeder;

class CardSetSeeder extends Seeder
{
    public function run(): void
    {
        $sets = [
            // Pokemon
            ['game' => 'pokemon', 'name' => 'Scarlet & Violet Base Set', 'set_code' => 'SVI', 'release_date' => '2023-03-31', 'series' => 'Scarlet & Violet'],
            ['game' => 'pokemon', 'name' => 'Paldea Evolved', 'set_code' => 'PAL', 'release_date' => '2023-06-09', 'series' => 'Scarlet & Violet'],
            ['game' => 'pokemon', 'name' => 'Obsidian Flames', 'set_code' => 'OBF', 'release_date' => '2023-08-11', 'series' => 'Scarlet & Violet'],
            ['game' => 'pokemon', 'name' => 'Paradox Rift', 'set_code' => 'PAR', 'release_date' => '2023-11-03', 'series' => 'Scarlet & Violet'],
            ['game' => 'pokemon', 'name' => 'Temporal Forces', 'set_code' => 'TEF', 'release_date' => '2024-03-22', 'series' => 'Scarlet & Violet'],
            ['game' => 'pokemon', 'name' => 'Twilight Masquerade', 'set_code' => 'TWM', 'release_date' => '2024-05-24', 'series' => 'Scarlet & Violet'],
            ['game' => 'pokemon', 'name' => 'Stellar Crown', 'set_code' => 'SCR', 'release_date' => '2024-09-13', 'series' => 'Scarlet & Violet'],
            ['game' => 'pokemon', 'name' => 'Surging Sparks', 'set_code' => 'SSP', 'release_date' => '2024-11-08', 'series' => 'Scarlet & Violet'],
            // MTG
            ['game' => 'mtg', 'name' => 'Murders at Karlov Manor', 'set_code' => 'MKM', 'release_date' => '2024-02-09', 'series' => 'Standard'],
            ['game' => 'mtg', 'name' => 'Outlaws of Thunder Junction', 'set_code' => 'OTJ', 'release_date' => '2024-04-19', 'series' => 'Standard'],
            ['game' => 'mtg', 'name' => 'Bloomburrow', 'set_code' => 'BLB', 'release_date' => '2024-08-02', 'series' => 'Standard'],
            ['game' => 'mtg', 'name' => 'Duskmourn: House of Horror', 'set_code' => 'DSK', 'release_date' => '2024-09-27', 'series' => 'Standard'],
            ['game' => 'mtg', 'name' => 'Foundations', 'set_code' => 'FDN', 'release_date' => '2024-11-15', 'series' => 'Standard'],
            // Yu-Gi-Oh
            ['game' => 'yugioh', 'name' => 'Phantom Nightmare', 'set_code' => 'PHNI', 'release_date' => '2024-02-08', 'series' => 'OCG/TCG'],
            ['game' => 'yugioh', 'name' => 'Infinite Forbidden', 'set_code' => 'INFI', 'release_date' => '2024-07-18', 'series' => 'OCG/TCG'],
            // One Piece
            ['game' => 'one_piece', 'name' => 'Wings of the Captain', 'set_code' => 'OP06', 'release_date' => '2024-01-26', 'series' => 'OP Series'],
            ['game' => 'one_piece', 'name' => 'Five Elders', 'set_code' => 'OP09', 'release_date' => '2024-10-25', 'series' => 'OP Series'],
            // Lorcana
            ['game' => 'lorcana', 'name' => 'Into the Inklands', 'set_code' => 'ITI', 'release_date' => '2024-03-08', 'series' => 'Lorcana'],
            ['game' => 'lorcana', 'name' => 'Shimmering Skies', 'set_code' => 'SSH', 'release_date' => '2024-08-09', 'series' => 'Lorcana'],
        ];

        foreach ($sets as $set) {
            CardSet::firstOrCreate(['game' => $set['game'], 'set_code' => $set['set_code']], $set);
        }

        $this->command->info('Card sets seeded.');
    }
}
