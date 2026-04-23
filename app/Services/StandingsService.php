<?php

namespace App\Services;

use App\Models\Pairing;
use App\Models\Tournament;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StandingsService
{
    private const MIN_WIN_RATE = 0.33;

    /**
     * Calculate full Swiss standings with all tiebreakers.
     * Tiebreaker order: Match Points → OMW% → GWP% → OGW%
     */
    public function calculateStandings(Tournament $tournament): Collection
    {
        $cacheKey = "standings.tournament.{$tournament->id}";

        return Cache::remember($cacheKey, 30, function () use ($tournament) {
            $registrations = $tournament->registrations()
                ->with(['player', 'pairingsAsPlayer1.round', 'pairingsAsPlayer2.round'])
                ->get();

            $stats = $registrations->mapWithKeys(fn($reg) => [$reg->id => $this->computeStats($reg)]);

            $standings = $registrations->map(function ($reg) use ($stats) {
                $s = $stats[$reg->id];
                $omw = $this->calculateOMW($reg, $stats);
                $ogw = $this->calculateOGW($reg, $stats);

                return [
                    'registration_id' => $reg->id,
                    'player_id' => $reg->player_id,
                    'player' => $reg->player,
                    'registration' => $reg,
                    'match_points' => $s['match_points'],
                    'matches_won' => $s['wins'],
                    'matches_lost' => $s['losses'],
                    'matches_drawn' => $s['draws'],
                    'games_won' => $s['games_won'],
                    'games_lost' => $s['games_lost'],
                    'games_played' => $s['games_played'],
                    'gwp' => max(self::MIN_WIN_RATE, $s['games_played'] > 0 ? $s['games_won'] / $s['games_played'] : 0),
                    'omw' => $omw,
                    'ogw' => $ogw,
                    'dropped' => $reg->dropped,
                    'byes' => $s['byes'],
                ];
            });

            return $standings->sort(function ($a, $b) {
                if ($a['match_points'] !== $b['match_points']) {
                    return $b['match_points'] <=> $a['match_points'];
                }
                if (abs($a['omw'] - $b['omw']) > 0.0001) {
                    return $b['omw'] <=> $a['omw'];
                }
                if (abs($a['gwp'] - $b['gwp']) > 0.0001) {
                    return $b['gwp'] <=> $a['gwp'];
                }
                return $b['ogw'] <=> $a['ogw'];
            })->values();
        });
    }

    /**
     * Compute raw match and game stats for a single registration.
     */
    public function computeStats(mixed $reg): array
    {
        $wins = $losses = $draws = $byes = 0;
        $gamesWon = $gamesLost = 0;

        foreach ($reg->pairingsAsPlayer1 as $pairing) {
            match($pairing->result) {
                'player1_win' => $wins++,
                'player2_win' => $losses++,
                'draw' => $draws++,
                'bye' => $byes++,
                default => null,
            };
            $gamesWon += $pairing->player1_games_won;
            $gamesLost += $pairing->player2_games_won;
        }

        foreach ($reg->pairingsAsPlayer2 as $pairing) {
            match($pairing->result) {
                'player2_win' => $wins++,
                'player1_win' => $losses++,
                'draw' => $draws++,
                default => null,
            };
            $gamesWon += $pairing->player2_games_won;
            $gamesLost += $pairing->player1_games_won;
        }

        return [
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'byes' => $byes,
            'match_points' => ($wins * 3) + $draws + ($byes * 2),
            'games_won' => $gamesWon,
            'games_lost' => $gamesLost,
            'games_played' => $gamesWon + $gamesLost,
            'match_win_rate' => ($wins + $losses + $draws) > 0
                ? $wins / ($wins + $losses + $draws)
                : 0,
        ];
    }

    /**
     * Opponent Match Win Percentage — average of all opponents' match win rates (min 33%).
     */
    private function calculateOMW(mixed $reg, Collection $allStats): float
    {
        $opponentIds = $this->getOpponentRegistrationIds($reg);

        if ($opponentIds->isEmpty()) {
            return self::MIN_WIN_RATE;
        }

        $rates = $opponentIds->map(function ($oppId) use ($allStats) {
            $s = $allStats[$oppId] ?? null;
            return $s ? max(self::MIN_WIN_RATE, $s['match_win_rate']) : self::MIN_WIN_RATE;
        });

        return $rates->average();
    }

    /**
     * Opponent Game Win Percentage — average of all opponents' game win rates (min 33%).
     */
    private function calculateOGW(mixed $reg, Collection $allStats): float
    {
        $opponentIds = $this->getOpponentRegistrationIds($reg);

        if ($opponentIds->isEmpty()) {
            return self::MIN_WIN_RATE;
        }

        $rates = $opponentIds->map(function ($oppId) use ($allStats) {
            $s = $allStats[$oppId] ?? null;
            if (!$s || $s['games_played'] === 0) return self::MIN_WIN_RATE;
            return max(self::MIN_WIN_RATE, $s['games_won'] / $s['games_played']);
        });

        return $rates->average();
    }

    /**
     * Get all opponent registration IDs (excluding byes).
     */
    private function getOpponentRegistrationIds(mixed $reg): Collection
    {
        $ids = collect();

        foreach ($reg->pairingsAsPlayer1 as $pairing) {
            if ($pairing->player2_registration_id) {
                $ids->push($pairing->player2_registration_id);
            }
        }
        foreach ($reg->pairingsAsPlayer2 as $pairing) {
            $ids->push($pairing->player1_registration_id);
        }

        return $ids->unique()->values();
    }

    /**
     * Bust the standings cache (call after recording results).
     */
    public function clearCache(Tournament $tournament): void
    {
        Cache::forget("standings.tournament.{$tournament->id}");
    }
}
