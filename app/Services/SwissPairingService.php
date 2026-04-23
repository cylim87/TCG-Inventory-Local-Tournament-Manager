<?php

namespace App\Services;

use App\Models\Pairing;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Models\TournamentRound;
use Illuminate\Support\Collection;

class SwissPairingService
{
    /**
     * Generate pairings for a new round.
     * Round 1 is random; subsequent rounds use Swiss algorithm.
     */
    public function generatePairings(Tournament $tournament, TournamentRound $round): Collection
    {
        $registrations = $this->getActivePlayers($tournament, $round->round_number);

        if ($registrations->isEmpty()) {
            throw new \RuntimeException('No active players to pair.');
        }

        $previousPairings = $this->buildPreviousPairingsMap($tournament);

        $pairings = $round->round_number === 1
            ? $this->randomPairings($registrations)
            : $this->swissPairings($registrations, $previousPairings);

        $this->savePairings($round, $pairings);

        return $round->pairings()->with(['player1Registration.player', 'player2Registration.player'])->get();
    }

    /**
     * Retrieve active (non-dropped) players sorted by Swiss standings for pairing.
     */
    private function getActivePlayers(Tournament $tournament, int $roundNumber): Collection
    {
        $registrations = $tournament->registrations()
            ->with(['player', 'pairingsAsPlayer1', 'pairingsAsPlayer2'])
            ->where('dropped', false)
            ->get();

        if ($roundNumber === 1) {
            return $registrations->shuffle();
        }

        $standings = app(StandingsService::class)->calculateStandings($tournament);
        $orderedIds = $standings->pluck('registration_id');

        return $registrations->sortBy(fn($r) => $orderedIds->search($r->id))->values();
    }

    /**
     * Round 1: completely random pairings.
     */
    private function randomPairings(Collection $players): array
    {
        $shuffled = $players->shuffle()->values();
        $pairings = [];

        while ($shuffled->count() >= 2) {
            $pairings[] = [$shuffled->shift(), $shuffled->shift()];
        }

        if ($shuffled->isNotEmpty()) {
            $pairings[] = [$shuffled->first(), null];
        }

        return $pairings;
    }

    /**
     * Swiss algorithm: pair players by point group, avoiding rematches.
     */
    private function swissPairings(Collection $players, array $previousPairings): array
    {
        $pairings = [];
        $byeEligible = null;

        // Group by match points (already sorted by standings)
        $groups = [];
        foreach ($players as $player) {
            $pts = $player->match_points;
            $groups[$pts][] = $player;
        }
        krsort($groups); // highest points first

        $unpaired = collect();

        foreach ($groups as $points => $group) {
            $group = collect($group)->merge($unpaired)->values();
            $unpaired = collect();

            while ($group->count() >= 2) {
                $player1 = $group->shift();
                $paired = false;

                foreach ($group as $idx => $player2) {
                    if (!$this->havePlayed($player1->id, $player2->id, $previousPairings)) {
                        $pairings[] = [$player1, $player2];
                        $group->forget($idx);
                        $group = $group->values();
                        $paired = true;
                        break;
                    }
                }

                if (!$paired) {
                    $unpaired->push($player1);
                }
            }

            if ($group->count() === 1) {
                $unpaired = $unpaired->merge($group);
            }
        }

        // Handle leftover unpaired players (last resort: pair even if rematch)
        while ($unpaired->count() >= 2) {
            $pairings[] = [$unpaired->shift(), $unpaired->shift()];
        }

        // Remaining one player gets a bye
        if ($unpaired->isNotEmpty()) {
            $pairings[] = [$unpaired->first(), null];
        }

        return $pairings;
    }

    /**
     * Check if two players have already played each other.
     */
    private function havePlayed(int $reg1Id, int $reg2Id, array $previousPairings): bool
    {
        return isset($previousPairings[$reg1Id][$reg2Id]) || isset($previousPairings[$reg2Id][$reg1Id]);
    }

    /**
     * Build a lookup map of previous pairings: [regId => [opponentRegId => true]].
     */
    private function buildPreviousPairingsMap(Tournament $tournament): array
    {
        $map = [];

        $pairings = Pairing::whereHas('round', fn($q) => $q->where('tournament_id', $tournament->id))
            ->whereNotNull('player2_registration_id')
            ->get();

        foreach ($pairings as $pairing) {
            $p1 = $pairing->player1_registration_id;
            $p2 = $pairing->player2_registration_id;
            $map[$p1][$p2] = true;
            $map[$p2][$p1] = true;
        }

        return $map;
    }

    /**
     * Persist the generated pairings to the database.
     */
    private function savePairings(TournamentRound $round, array $pairings): void
    {
        $table = 1;
        foreach ($pairings as [$player1, $player2]) {
            Pairing::create([
                'tournament_round_id' => $round->id,
                'table_number' => $table++,
                'player1_registration_id' => $player1->id,
                'player2_registration_id' => $player2?->id,
                'result' => $player2 === null ? 'bye' : 'pending',
                'submitted_at' => $player2 === null ? now() : null,
            ]);
        }
    }
}
