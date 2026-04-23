<?php

namespace App\Http\Controllers;

use App\Models\Pairing;
use App\Models\Tournament;
use App\Models\TournamentRound;
use App\Services\StandingsService;
use Illuminate\Http\Request;

class PairingController extends Controller
{
    public function __construct(private StandingsService $standingsService) {}

    public function update(Request $request, Tournament $tournament, TournamentRound $round, Pairing $pairing)
    {
        $request->validate([
            'result' => 'required|in:player1_win,player2_win,draw,double_loss',
            'player1_games_won' => 'nullable|integer|min:0|max:5',
            'player2_games_won' => 'nullable|integer|min:0|max:5',
            'draws' => 'nullable|integer|min:0|max:3',
            'is_intentional_draw' => 'boolean',
        ]);

        if ($pairing->result === 'bye') {
            return back()->with('error', 'Cannot update a bye pairing.');
        }

        $pairing->update([
            'result' => $request->result,
            'player1_games_won' => $request->player1_games_won ?? 0,
            'player2_games_won' => $request->player2_games_won ?? 0,
            'draws' => $request->draws ?? 0,
            'is_intentional_draw' => $request->boolean('is_intentional_draw'),
            'submitted_at' => now(),
        ]);

        $this->standingsService->clearCache($tournament);

        return back()->with('success', "Table {$pairing->table_number} result recorded.");
    }

    public function reset(Tournament $tournament, TournamentRound $round, Pairing $pairing)
    {
        if ($round->status === 'completed') {
            return back()->with('error', 'Cannot modify results in a completed round.');
        }

        if ($pairing->result === 'bye') {
            return back()->with('error', 'Cannot reset a bye.');
        }

        $pairing->update([
            'result' => 'pending',
            'player1_games_won' => 0,
            'player2_games_won' => 0,
            'draws' => 0,
            'submitted_at' => null,
        ]);

        $this->standingsService->clearCache($tournament);
        return back()->with('success', 'Result reset to pending.');
    }
}
