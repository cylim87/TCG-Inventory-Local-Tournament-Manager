<?php

namespace Database\Seeders;

use App\Models\Pairing;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Models\TournamentRound;
use App\Models\User;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;

        // --- Completed Tournament (3 rounds, full results) ---
        $past = Tournament::firstOrCreate(
            ['name' => 'Weekly Pokemon Standard #41'],
            [
                'game' => 'pokemon',
                'format' => 'standard',
                'date' => now()->subDays(7)->toDateString(),
                'start_time' => '18:00:00',
                'entry_fee' => 10.00,
                'prize_pool' => 60.00,
                'rounds' => 3,
                'top_cut' => 0,
                'status' => 'completed',
                'user_id' => $admin->id,
            ]
        );

        $pastPlayers = Player::take(8)->get();
        $regs = [];
        foreach ($pastPlayers as $player) {
            $regs[] = TournamentRegistration::firstOrCreate(
                ['tournament_id' => $past->id, 'player_id' => $player->id],
                ['paid' => true]
            );
        }

        // Seed 3 completed rounds for the past tournament
        $this->seedCompletedRounds($past, $regs);

        // --- Active Tournament (registration open) ---
        Tournament::firstOrCreate(
            ['name' => 'Weekly Pokemon Standard #42'],
            [
                'game' => 'pokemon',
                'format' => 'standard',
                'date' => now()->addDays(3)->toDateString(),
                'start_time' => '18:00:00',
                'entry_fee' => 10.00,
                'prize_pool' => 0.00,
                'rounds' => null,
                'top_cut' => 4,
                'status' => 'registration',
                'description' => 'Standard format. Bring a 60-card deck. Top 4 cut to single elimination.',
                'user_id' => $admin->id,
            ]
        );

        // --- MTG Modern Weekly ---
        $mtgTournament = Tournament::firstOrCreate(
            ['name' => 'MTG Modern Showcase'],
            [
                'game' => 'mtg',
                'format' => 'modern',
                'date' => now()->addDays(10)->toDateString(),
                'start_time' => '14:00:00',
                'entry_fee' => 15.00,
                'prize_pool' => 0.00,
                'rounds' => null,
                'top_cut' => 8,
                'status' => 'registration',
                'description' => 'Modern format. 75-card deck + sideboard. Top 8 cut.',
                'user_id' => $admin->id,
            ]
        );

        // Register some MTG players
        $mtgPlayers = Player::whereIn('preferred_game', ['mtg', 'pokemon'])->take(6)->get();
        foreach ($mtgPlayers as $player) {
            TournamentRegistration::firstOrCreate(
                ['tournament_id' => $mtgTournament->id, 'player_id' => $player->id],
                ['paid' => false]
            );
        }

        // --- Yu-Gi-Oh Weekly ---
        Tournament::firstOrCreate(
            ['name' => 'Yu-Gi-Oh! Local #18'],
            [
                'game' => 'yugioh',
                'format' => 'standard',
                'date' => now()->subDays(14)->toDateString(),
                'start_time' => '17:30:00',
                'entry_fee' => 8.00,
                'prize_pool' => 40.00,
                'rounds' => 4,
                'top_cut' => 0,
                'status' => 'completed',
                'user_id' => $admin->id,
            ]
        );

        $this->command->info('Tournaments seeded.');
    }

    private function seedCompletedRounds(Tournament $tournament, array $regs): void
    {
        $results = [
            // Round 1 pairings: [p1_idx, p2_idx, result, p1_gw, p2_gw]
            1 => [[0,1,'player1_win',2,1],[2,3,'player2_win',0,2],[4,5,'player1_win',2,0],[6,7,'player1_win',2,1]],
            2 => [[0,2,'player1_win',2,0],[1,4,'player2_win',1,2],[3,6,'player1_win',2,1],[5,7,'draw',1,1]],
            3 => [[0,3,'player1_win',2,0],[2,6,'player1_win',2,1],[1,5,'player1_win',2,0],[4,7,'player2_win',0,2]],
        ];

        foreach ($results as $roundNum => $pairings) {
            $round = TournamentRound::firstOrCreate(
                ['tournament_id' => $tournament->id, 'round_number' => $roundNum],
                [
                    'status' => 'completed',
                    'started_at' => now()->subDays(7)->addHours($roundNum),
                    'completed_at' => now()->subDays(7)->addHours($roundNum + 1),
                ]
            );

            foreach ($pairings as $table => [$p1, $p2, $result, $p1gw, $p2gw]) {
                Pairing::firstOrCreate(
                    [
                        'tournament_round_id' => $round->id,
                        'player1_registration_id' => $regs[$p1]->id,
                    ],
                    [
                        'player2_registration_id' => $regs[$p2]->id,
                        'table_number' => $table + 1,
                        'result' => $result,
                        'player1_games_won' => $p1gw,
                        'player2_games_won' => $p2gw,
                        'draws' => $result === 'draw' ? 1 : 0,
                        'submitted_at' => now()->subDays(7)->addHours($roundNum + 0.5),
                    ]
                );
            }
        }
    }
}
