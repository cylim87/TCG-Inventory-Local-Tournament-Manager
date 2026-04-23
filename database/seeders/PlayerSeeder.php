<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $players = [
            ['first_name' => 'Alex',    'last_name' => 'Chen',       'email' => 'alex.chen@example.com',    'player_number' => 'P-00001', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Jordan',  'last_name' => 'Williams',   'email' => 'jordan.w@example.com',     'player_number' => 'P-00002', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Sam',     'last_name' => 'Nguyen',     'email' => 'sam.nguyen@example.com',   'player_number' => 'P-00003', 'preferred_game' => 'mtg'],
            ['first_name' => 'Taylor',  'last_name' => 'Brown',      'email' => 'taylor.b@example.com',     'player_number' => 'P-00004', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Morgan',  'last_name' => 'Davis',      'email' => 'morgan.d@example.com',     'player_number' => 'P-00005', 'preferred_game' => 'mtg'],
            ['first_name' => 'Riley',   'last_name' => 'Martinez',   'email' => 'riley.m@example.com',      'player_number' => 'P-00006', 'preferred_game' => 'yugioh'],
            ['first_name' => 'Casey',   'last_name' => 'Thompson',   'email' => 'casey.t@example.com',      'player_number' => 'P-00007', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Drew',    'last_name' => 'Garcia',     'email' => 'drew.g@example.com',       'player_number' => 'P-00008', 'preferred_game' => 'one_piece'],
            ['first_name' => 'Jamie',   'last_name' => 'Anderson',   'email' => 'jamie.a@example.com',      'player_number' => 'P-00009', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Quinn',   'last_name' => 'Jackson',    'email' => 'quinn.j@example.com',      'player_number' => 'P-00010', 'preferred_game' => 'lorcana'],
            ['first_name' => 'Avery',   'last_name' => 'White',      'email' => 'avery.w@example.com',      'player_number' => 'P-00011', 'preferred_game' => 'mtg'],
            ['first_name' => 'Parker',  'last_name' => 'Harris',     'email' => 'parker.h@example.com',     'player_number' => 'P-00012', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Skyler',  'last_name' => 'Lewis',      'email' => 'skyler.l@example.com',     'player_number' => 'P-00013', 'preferred_game' => 'yugioh'],
            ['first_name' => 'Blake',   'last_name' => 'Robinson',   'email' => 'blake.r@example.com',      'player_number' => 'P-00014', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Reese',   'last_name' => 'Walker',     'email' => 'reese.w@example.com',      'player_number' => 'P-00015', 'preferred_game' => 'mtg'],
            ['first_name' => 'Logan',   'last_name' => 'Hall',       'email' => 'logan.h@example.com',      'player_number' => 'P-00016', 'preferred_game' => 'one_piece'],
            ['first_name' => 'Cameron', 'last_name' => 'Young',      'email' => 'cam.y@example.com',        'player_number' => 'P-00017', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Peyton',  'last_name' => 'King',       'email' => 'peyton.k@example.com',     'player_number' => 'P-00018', 'preferred_game' => 'lorcana'],
            ['first_name' => 'Finley',  'last_name' => 'Wright',     'email' => 'finley.w@example.com',     'player_number' => 'P-00019', 'preferred_game' => 'fab'],
            ['first_name' => 'Rowan',   'last_name' => 'Scott',      'email' => 'rowan.s@example.com',      'player_number' => 'P-00020', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Hayden',  'last_name' => 'Green',      'email' => null,                       'player_number' => 'P-00021', 'preferred_game' => 'yugioh'],
            ['first_name' => 'Dakota',  'last_name' => 'Baker',      'email' => null,                       'player_number' => 'P-00022', 'preferred_game' => 'pokemon'],
            ['first_name' => 'Emery',   'last_name' => 'Adams',      'email' => 'emery.a@example.com',      'player_number' => 'P-00023', 'preferred_game' => 'mtg'],
            ['first_name' => 'Kendall', 'last_name' => 'Nelson',     'email' => 'kendall.n@example.com',    'player_number' => 'P-00024', 'preferred_game' => 'pokemon'],
        ];

        foreach ($players as $p) {
            Player::firstOrCreate(['player_number' => $p['player_number']], $p);
        }

        $this->command->info(count($players) . ' players seeded.');
    }
}
