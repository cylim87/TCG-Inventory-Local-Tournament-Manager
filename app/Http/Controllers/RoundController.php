<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentRound;
use App\Services\StandingsService;
use App\Services\SwissPairingService;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    public function __construct(
        private SwissPairingService $pairingService,
        private StandingsService $standingsService
    ) {}

    public function store(Request $request, Tournament $tournament)
    {
        if ($tournament->status !== 'active') {
            return back()->with('error', 'Tournament must be active to start a new round.');
        }

        $lastRound = $tournament->rounds()->latest('round_number')->first();

        if ($lastRound && $lastRound->status !== 'completed') {
            return back()->with('error', 'Current round must be completed before starting a new one.');
        }

        if ($tournament->rounds && $tournament->current_round_number >= $tournament->rounds) {
            return back()->with('error', 'All rounds have been played. Complete the tournament or add a top cut.');
        }

        $round = TournamentRound::create([
            'tournament_id' => $tournament->id,
            'round_number' => ($lastRound?->round_number ?? 0) + 1,
            'status' => 'active',
            'started_at' => now(),
        ]);

        $this->pairingService->generatePairings($tournament, $round);
        $this->standingsService->clearCache($tournament);

        return redirect()->route('rounds.show', [$tournament, $round])->with('success', "Round {$round->round_number} pairings generated.");
    }

    public function show(Tournament $tournament, TournamentRound $round)
    {
        $round->load(['pairings.player1Registration.player', 'pairings.player2Registration.player']);

        return view('tournaments.rounds.show', compact('tournament', 'round'));
    }

    public function complete(Tournament $tournament, TournamentRound $round)
    {
        if ($round->pending_pairings_count > 0) {
            return back()->with('error', "Cannot complete round — {$round->pending_pairings_count} match(es) still pending.");
        }

        $round->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->standingsService->clearCache($tournament);

        return redirect()->route('tournaments.show', $tournament)->with('success', "Round {$round->round_number} completed.");
    }
}
