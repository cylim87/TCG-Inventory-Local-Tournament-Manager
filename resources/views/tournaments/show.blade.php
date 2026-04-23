@extends('layouts.app')
@section('title', $tournament->name)

@section('actions')
    @if($tournament->status === 'registration')
    <form method="POST" action="{{ route('tournaments.start', $tournament) }}" class="inline">
        @csrf
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
            Start Tournament
        </button>
    </form>
    @endif
    @if($tournament->status === 'active')
    <form method="POST" action="{{ route('tournaments.rounds.store', $tournament) }}" class="inline">
        @csrf
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
            + New Round
        </button>
    </form>
    @endif
    @if(in_array($tournament->status, ['active', 'top_cut']))
    <form method="POST" action="{{ route('tournaments.complete', $tournament) }}" class="inline"
          onsubmit="return confirm('Mark tournament as completed?')">
        @csrf
        <button class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-sm">Finish</button>
    </form>
    @endif
    <a href="{{ route('tournaments.edit', $tournament) }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-sm">Edit</a>
@endsection

@section('content')
<div class="space-y-6 mt-2" x-data="{ tab: 'players' }">

    {{-- Header --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <div class="flex flex-wrap items-center gap-4 justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-white">{{ $tournament->name }}</h2>
                    <span class="inline-block px-2 py-0.5 rounded text-xs
                        @if($tournament->status === 'registration') bg-blue-900/50 text-blue-300
                        @elseif($tournament->status === 'active') bg-green-900/50 text-green-300
                        @elseif($tournament->status === 'top_cut') bg-yellow-900/50 text-yellow-300
                        @elseif($tournament->status === 'completed') bg-slate-700 text-slate-400
                        @else bg-red-900/50 text-red-300 @endif">
                        {{ ucfirst(str_replace('_', ' ', $tournament->status)) }}
                    </span>
                </div>
                <p class="text-sm text-slate-400">
                    {{ \App\Models\CardSet::gameLabel($tournament->game) }} ·
                    {{ \App\Models\Tournament::formatLabel($tournament->format) }} ·
                    {{ $tournament->date->format('M j, Y') }} at {{ $tournament->start_time }}
                </p>
            </div>
            <div class="flex gap-6 text-center">
                <div>
                    <p class="text-2xl font-bold text-white">{{ $tournament->player_count }}</p>
                    <p class="text-xs text-slate-400">Players</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $tournament->current_round_number }}</p>
                    <p class="text-xs text-slate-400">of {{ $tournament->rounds ?? '?' }} Rounds</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-400">${{ number_format($tournament->total_revenue, 2) }}</p>
                    <p class="text-xs text-slate-400">Revenue</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-slate-700 gap-1">
        @foreach(['players' => 'Players', 'rounds' => 'Rounds', 'standings' => 'Standings'] as $key => $label)
        <button @click="tab = '{{ $key }}'"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                :class="tab === '{{ $key }}' ? 'border-indigo-500 text-white' : 'border-transparent text-slate-400 hover:text-slate-300'">
            {{ $label }}
            @if($key === 'players') ({{ $tournament->player_count }}) @endif
            @if($key === 'rounds') ({{ $tournament->rounds()->count() }}) @endif
        </button>
        @endforeach
    </div>

    {{-- Players Tab --}}
    <div x-show="tab === 'players'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Registration list --}}
            <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-700 flex items-center justify-between">
                    <h3 class="font-semibold text-white">Registered Players</h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                            <th class="px-4 py-2 font-medium">#</th>
                            <th class="px-4 py-2 font-medium">Player</th>
                            <th class="px-4 py-2 font-medium">Deck</th>
                            <th class="px-4 py-2 font-medium text-center">Paid</th>
                            <th class="px-4 py-2 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse($tournament->registrations as $i => $reg)
                        <tr class="{{ $reg->dropped ? 'opacity-50' : '' }}">
                            <td class="px-4 py-2 text-slate-500 text-xs">{{ $i + 1 }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('players.show', $reg->player) }}" class="text-white hover:text-indigo-400 text-sm">
                                    {{ $reg->player->full_name }}
                                </a>
                                @if($reg->dropped)
                                <span class="text-xs text-red-400 ml-1">(dropped R{{ $reg->drop_round }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-slate-400 text-xs">{{ $reg->deck_name ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">
                                <form method="POST" action="{{ route('tournaments.update-paid', [$tournament, $reg]) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="paid" value="{{ $reg->paid ? '0' : '1' }}">
                                    <button class="text-{{ $reg->paid ? 'emerald' : 'slate' }}-400 hover:text-{{ $reg->paid ? 'emerald' : 'slate' }}-300 text-lg" title="{{ $reg->paid ? 'Mark unpaid' : 'Mark paid' }}">
                                        {{ $reg->paid ? '✓' : '○' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-2">
                                @if($tournament->status === 'registration')
                                <form method="POST" action="{{ route('tournaments.unregister', [$tournament, $reg]) }}" class="inline"
                                      onsubmit="return confirm('Unregister {{ $reg->player->full_name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-400 hover:text-red-300">Remove</button>
                                </form>
                                @elseif(!$reg->dropped && $tournament->status === 'active')
                                <form method="POST" action="{{ route('tournaments.drop', [$tournament, $reg]) }}" class="inline"
                                      onsubmit="return confirm('Drop {{ $reg->player->full_name }}?')">
                                    @csrf
                                    <button class="text-xs text-amber-400 hover:text-amber-300">Drop</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">No players registered yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Register new player --}}
            @if($tournament->status === 'registration')
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                <h3 class="font-semibold text-white mb-4">Add Player</h3>
                <form method="POST" action="{{ route('tournaments.register', $tournament) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Player</label>
                        <select name="player_id" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                            <option value="">— Select Player —</option>
                            @foreach($unregisteredPlayers as $p)
                            <option value="{{ $p->id }}">{{ $p->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Deck Name (optional)</label>
                        <input type="text" name="deck_name" placeholder="e.g. Charizard ex"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="paid" value="1" class="rounded">
                        Entry fee paid
                    </label>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Register</button>
                </form>
                <div class="mt-4 pt-3 border-t border-slate-700">
                    <a href="{{ route('players.create') }}" class="text-xs text-indigo-400 hover:text-indigo-300">+ Create new player →</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Rounds Tab --}}
    <div x-show="tab === 'rounds'" x-cloak class="space-y-4">
        @forelse($tournament->rounds as $round)
        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-700">
                <div class="flex items-center gap-3">
                    <h3 class="font-semibold text-white">Round {{ $round->round_number }}</h3>
                    <span class="inline-block px-2 py-0.5 rounded text-xs
                        @if($round->status === 'active') bg-green-900/50 text-green-300
                        @elseif($round->status === 'completed') bg-slate-700 text-slate-400
                        @else bg-yellow-900/50 text-yellow-300 @endif">
                        {{ ucfirst($round->status) }}
                    </span>
                    @if($round->status === 'active' && $round->pending_pairings_count > 0)
                    <span class="text-xs text-amber-400">{{ $round->pending_pairings_count }} pending</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('tournaments.rounds.show', [$tournament, $round]) }}" class="text-xs text-indigo-400 hover:text-indigo-300">View Pairings</a>
                    @if($round->status === 'active' && $round->pending_pairings_count === 0)
                    <form method="POST" action="{{ route('tournaments.rounds.complete', [$tournament, $round]) }}" class="inline">
                        @csrf
                        <button class="text-xs bg-emerald-700 hover:bg-emerald-600 text-white px-3 py-1 rounded-lg">Complete Round</button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="px-5 py-3">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs text-slate-400">
                    @foreach($round->pairings->take(8) as $pairing)
                    <div class="bg-slate-900/40 rounded px-2 py-1.5">
                        <p class="text-slate-500 mb-0.5">Table {{ $pairing->table_number }}</p>
                        <p class="text-slate-300 truncate">{{ $pairing->player1Registration->player->full_name }}</p>
                        @if($pairing->isBye())
                        <p class="text-slate-500 italic">— BYE —</p>
                        @else
                        <p class="text-slate-400 truncate">{{ $pairing->player2Registration->player->full_name }}</p>
                        @endif
                        <p class="mt-1 font-medium
                            {{ $pairing->result === 'pending' ? 'text-amber-400' : 'text-emerald-400' }}">
                            @if($pairing->result === 'pending') Pending
                            @elseif($pairing->result === 'bye') Bye
                            @elseif($pairing->result === 'player1_win') P1 Win {{ $pairing->player1_games_won }}-{{ $pairing->player2_games_won }}
                            @elseif($pairing->result === 'player2_win') P2 Win {{ $pairing->player2_games_won }}-{{ $pairing->player1_games_won }}
                            @elseif($pairing->result === 'draw') Draw
                            @else {{ $pairing->result }}
                            @endif
                        </p>
                    </div>
                    @endforeach
                    @if($round->pairings->count() > 8)
                    <div class="bg-slate-900/40 rounded px-2 py-1.5 flex items-center justify-center">
                        <a href="{{ route('tournaments.rounds.show', [$tournament, $round]) }}" class="text-indigo-400 text-xs">
                            +{{ $round->pairings->count() - 8 }} more →
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-slate-500">
            @if($tournament->status === 'active')
            Use the "New Round" button above to generate pairings.
            @else
            Start the tournament to begin pairing players.
            @endif
        </div>
        @endforelse
    </div>

    {{-- Standings Tab --}}
    <div x-show="tab === 'standings'" x-cloak>
        @if($standings)
        <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                            <th class="px-4 py-3 font-medium w-10">Rank</th>
                            <th class="px-4 py-3 font-medium">Player</th>
                            <th class="px-4 py-3 font-medium text-center">Pts</th>
                            <th class="px-4 py-3 font-medium text-center">W-L-D</th>
                            <th class="px-4 py-3 font-medium text-right">OMW%</th>
                            <th class="px-4 py-3 font-medium text-right">GW%</th>
                            <th class="px-4 py-3 font-medium text-right">OGW%</th>
                            <th class="px-4 py-3 font-medium text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($standings as $rank => $s)
                        <tr class="{{ $s['dropped'] ? 'opacity-40' : '' }} hover:bg-slate-700/30">
                            <td class="px-4 py-3 text-slate-400 font-mono text-xs">
                                @if($rank < 3 && !$s['dropped'])
                                <span class="text-{{ $rank === 0 ? 'yellow' : ($rank === 1 ? 'slate' : 'amber') }}-400 font-bold">{{ $rank + 1 }}</span>
                                @else
                                {{ $rank + 1 }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('players.show', $s['player']) }}" class="text-white hover:text-indigo-400 font-medium">
                                    {{ $s['player']->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-white">{{ $s['match_points'] }}</td>
                            <td class="px-4 py-3 text-center text-slate-300">
                                {{ $s['matches_won'] }}-{{ $s['matches_lost'] }}-{{ $s['matches_drawn'] }}
                                @if($s['byes'] > 0) <span class="text-slate-500 text-xs">({{ $s['byes'] }}bye)</span> @endif
                            </td>
                            <td class="px-4 py-3 text-right text-slate-400 font-mono text-xs">{{ number_format($s['omw'] * 100, 1) }}%</td>
                            <td class="px-4 py-3 text-right text-slate-400 font-mono text-xs">{{ number_format($s['gwp'] * 100, 1) }}%</td>
                            <td class="px-4 py-3 text-right text-slate-400 font-mono text-xs">{{ number_format($s['ogw'] * 100, 1) }}%</td>
                            <td class="px-4 py-3 text-center">
                                @if($s['dropped'])
                                <span class="text-xs text-red-400">Dropped</span>
                                @else
                                <span class="text-xs text-emerald-400">Active</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-700 text-xs text-slate-500">
                Tiebreakers: Match Points → Opponent Match Win% → Game Win% → Opponent Game Win%
            </div>
        </div>
        @else
        <div class="text-center py-12 text-slate-500">Standings will appear once the tournament is active.</div>
        @endif
    </div>

</div>
@endsection
