<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Models\Player;
use App\Services\StandingsService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = Player::when($request->search, fn($q) => $q->search($request->search))
            ->when($request->game, fn($q) => $q->where('preferred_game', $request->game))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        return view('players.index', compact('players'));
    }

    public function show(Player $player)
    {
        $player->load(['registrations.tournament']);

        $history = $player->registrations()
            ->with(['tournament'])
            ->orderByDesc(fn($q) => $q->join('tournaments', 'tournaments.id', '=', 'tournament_registrations.tournament_id')->orderByDesc('tournaments.date'))
            ->latest()
            ->get();

        return view('players.show', compact('player', 'history'));
    }

    public function create()
    {
        return view('players.create');
    }

    public function store(StorePlayerRequest $request)
    {
        $player = Player::create($request->validated());
        return redirect()->route('players.show', $player)->with('success', "Player \"{$player->full_name}\" registered.");
    }

    public function edit(Player $player)
    {
        return view('players.edit', compact('player'));
    }

    public function update(StorePlayerRequest $request, Player $player)
    {
        $player->update($request->validated());
        return redirect()->route('players.show', $player)->with('success', 'Player updated.');
    }

    public function destroy(Player $player)
    {
        $player->update(['is_active' => false]);
        return redirect()->route('players.index')->with('success', 'Player deactivated.');
    }
}
