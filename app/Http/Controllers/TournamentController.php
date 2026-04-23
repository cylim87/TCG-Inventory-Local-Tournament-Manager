<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTournamentRequest;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use App\Services\StandingsService;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function __construct(private StandingsService $standingsService) {}

    public function index(Request $request)
    {
        $tournaments = Tournament::withCount('registrations')
            ->when($request->game, fn($q) => $q->where('game', $request->game))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        return view('tournaments.index', compact('tournaments'));
    }

    public function show(Tournament $tournament)
    {
        $tournament->load(['registrations.player', 'rounds.pairings.player1Registration.player', 'rounds.pairings.player2Registration.player']);

        $standings = null;
        if (in_array($tournament->status, ['active', 'top_cut', 'completed'])) {
            $standings = $this->standingsService->calculateStandings($tournament);
        }

        $unregisteredPlayers = Player::active()
            ->whereNotIn('id', $tournament->registrations->pluck('player_id'))
            ->orderBy('last_name')
            ->get();

        return view('tournaments.show', compact('tournament', 'standings', 'unregisteredPlayers'));
    }

    public function create()
    {
        return view('tournaments.create', [
            'games' => $this->gameOptions(),
            'formats' => $this->formatOptions(),
        ]);
    }

    public function store(StoreTournamentRequest $request)
    {
        $tournament = Tournament::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('tournaments.show', $tournament)->with('success', "Tournament \"{$tournament->name}\" created.");
    }

    public function edit(Tournament $tournament)
    {
        return view('tournaments.edit', [
            'tournament' => $tournament,
            'games' => $this->gameOptions(),
            'formats' => $this->formatOptions(),
        ]);
    }

    public function update(StoreTournamentRequest $request, Tournament $tournament)
    {
        $tournament->update($request->validated());
        return redirect()->route('tournaments.show', $tournament)->with('success', 'Tournament updated.');
    }

    public function register(Request $request, Tournament $tournament)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'paid' => 'boolean',
            'deck_name' => 'nullable|string|max:100',
        ]);

        if ($tournament->max_players && $tournament->player_count >= $tournament->max_players) {
            return back()->with('error', 'Tournament is full.');
        }

        if ($tournament->status !== 'registration') {
            return back()->with('error', 'Registration is not open for this tournament.');
        }

        TournamentRegistration::firstOrCreate(
            ['tournament_id' => $tournament->id, 'player_id' => $request->player_id],
            ['paid' => $request->boolean('paid'), 'deck_name' => $request->deck_name]
        );

        $this->standingsService->clearCache($tournament);

        return back()->with('success', 'Player registered.');
    }

    public function unregister(Tournament $tournament, TournamentRegistration $registration)
    {
        if ($tournament->status !== 'registration') {
            return back()->with('error', 'Cannot unregister after tournament has started.');
        }

        $registration->delete();
        $this->standingsService->clearCache($tournament);

        return back()->with('success', 'Player unregistered.');
    }

    public function drop(Tournament $tournament, TournamentRegistration $registration)
    {
        $registration->update([
            'dropped' => true,
            'drop_round' => $tournament->current_round_number,
        ]);

        $this->standingsService->clearCache($tournament);
        return back()->with('success', 'Player dropped from tournament.');
    }

    public function start(Tournament $tournament)
    {
        if ($tournament->registrations()->count() < 2) {
            return back()->with('error', 'Need at least 2 players to start.');
        }

        $tournament->update([
            'status' => 'active',
            'rounds' => $tournament->rounds ?? $tournament->recommended_rounds,
        ]);

        return back()->with('success', 'Tournament started! Generate Round 1 pairings.');
    }

    public function complete(Tournament $tournament)
    {
        $tournament->update(['status' => 'completed']);
        return back()->with('success', 'Tournament marked as completed.');
    }

    public function updatePaid(Request $request, Tournament $tournament, TournamentRegistration $registration)
    {
        $registration->update(['paid' => $request->boolean('paid')]);
        return back()->with('success', 'Payment status updated.');
    }

    private function gameOptions(): array
    {
        return [
            'pokemon' => 'Pokémon',
            'mtg' => 'Magic: The Gathering',
            'yugioh' => 'Yu-Gi-Oh!',
            'one_piece' => 'One Piece',
            'lorcana' => 'Disney Lorcana',
            'fab' => 'Flesh and Blood',
            'digimon' => 'Digimon',
            'union_arena' => 'Union Arena',
            'other' => 'Other',
        ];
    }

    private function formatOptions(): array
    {
        return [
            'standard' => 'Standard',
            'expanded' => 'Expanded',
            'modern' => 'Modern',
            'legacy' => 'Legacy',
            'pioneer' => 'Pioneer',
            'vintage' => 'Vintage',
            'draft' => 'Booster Draft',
            'sealed' => 'Sealed Deck',
            'commander' => 'Commander',
            'pre_release' => 'Pre-Release',
            'limited' => 'Limited',
            'other' => 'Other',
        ];
    }
}
