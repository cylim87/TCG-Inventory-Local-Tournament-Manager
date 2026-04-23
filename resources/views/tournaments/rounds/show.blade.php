@extends('layouts.app')
@section('title', $tournament->name . ' — Round ' . $round->round_number)

@section('actions')
    <a href="{{ route('tournaments.show', $tournament) }}" class="text-slate-400 hover:text-white text-sm">← Back to Event</a>
    @if($round->status === 'active' && $round->pending_pairings_count === 0)
    <form method="POST" action="{{ route('tournaments.rounds.complete', [$tournament, $round]) }}" class="inline">
        @csrf
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm">Complete Round</button>
    </form>
    @endif
@endsection

@section('content')
<div class="space-y-4 mt-2">

    <div class="flex items-center gap-3 py-2">
        <h2 class="text-lg font-semibold text-white">Round {{ $round->round_number }} Pairings</h2>
        <span class="inline-block px-2 py-0.5 rounded text-xs
            {{ $round->status === 'active' ? 'bg-green-900/50 text-green-300' : 'bg-slate-700 text-slate-400' }}">
            {{ ucfirst($round->status) }}
        </span>
        @if($round->pending_pairings_count > 0)
        <span class="text-sm text-amber-400">{{ $round->pending_pairings_count }} matches remaining</span>
        @elseif($round->status === 'active')
        <span class="text-sm text-emerald-400">All results in — ready to complete!</span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($round->pairings as $pairing)
        <div class="bg-slate-800 border {{ $pairing->result === 'pending' ? 'border-slate-700' : 'border-slate-600' }} rounded-xl overflow-hidden"
             x-data="{ editing: false }">

            {{-- Table header --}}
            <div class="flex items-center justify-between px-4 py-2 bg-slate-900/40 border-b border-slate-700">
                <span class="text-xs font-bold text-slate-400">TABLE {{ $pairing->table_number }}</span>
                <span class="text-xs
                    @if($pairing->result === 'pending') text-amber-400
                    @elseif($pairing->result === 'bye') text-slate-400
                    @else text-emerald-400 @endif">
                    @if($pairing->result === 'pending') PENDING
                    @elseif($pairing->result === 'bye') BYE
                    @elseif($pairing->result === 'player1_win') P1 WIN
                    @elseif($pairing->result === 'player2_win') P2 WIN
                    @elseif($pairing->result === 'draw') DRAW
                    @else {{ strtoupper($pairing->result) }} @endif
                </span>
            </div>

            {{-- Players --}}
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium {{ $pairing->result === 'player1_win' || $pairing->result === 'bye' ? 'text-emerald-400' : 'text-white' }}">
                        {{ $pairing->player1Registration->player->full_name }}
                    </span>
                    @if($pairing->result !== 'pending' && $pairing->result !== 'bye')
                    <span class="text-lg font-bold text-white font-mono">{{ $pairing->player1_games_won }}</span>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    @if($pairing->isBye())
                    <span class="text-sm text-slate-500 italic">— BYE —</span>
                    @else
                    <span class="text-sm font-medium {{ $pairing->result === 'player2_win' ? 'text-emerald-400' : 'text-white' }}">
                        {{ $pairing->player2Registration->player->full_name }}
                    </span>
                    @if($pairing->result !== 'pending')
                    <span class="text-lg font-bold text-white font-mono">{{ $pairing->player2_games_won }}</span>
                    @endif
                    @endif
                </div>
            </div>

            {{-- Result entry (active rounds, non-bye pairings) --}}
            @if($round->status === 'active' && !$pairing->isBye())
            <div class="px-4 pb-3">
                @if($pairing->result !== 'pending')
                <form method="POST" action="{{ route('tournaments.pairings.reset', [$tournament, $round, $pairing]) }}" class="inline">
                    @csrf
                    <button class="text-xs text-slate-400 hover:text-slate-300">Reset result</button>
                </form>
                @else
                <button @click="editing = !editing" class="text-xs text-indigo-400 hover:text-indigo-300">Enter result</button>
                @endif
            </div>

            <div x-show="editing" x-cloak class="px-4 pb-4 border-t border-slate-700 pt-3">
                <form method="POST" action="{{ route('tournaments.pairings.update', [$tournament, $round, $pairing]) }}" class="space-y-3">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-3 gap-2 items-end">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">P1 Wins</label>
                            <input type="number" name="player1_games_won" value="2" min="0" max="3"
                                   class="w-full bg-slate-700 border border-slate-600 text-white rounded px-2 py-1 text-sm text-center">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Draws</label>
                            <input type="number" name="draws" value="0" min="0" max="3"
                                   class="w-full bg-slate-700 border border-slate-600 text-white rounded px-2 py-1 text-sm text-center">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">P2 Wins</label>
                            <input type="number" name="player2_games_won" value="0" min="0" max="3"
                                   class="w-full bg-slate-700 border border-slate-600 text-white rounded px-2 py-1 text-sm text-center">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Match Result</label>
                        <select name="result" required class="w-full bg-slate-700 border border-slate-600 text-white rounded px-2 py-1 text-sm">
                            <option value="player1_win">{{ $pairing->player1Registration->player->first_name }} wins</option>
                            <option value="player2_win">{{ $pairing->player2Registration->player->first_name }} wins</option>
                            <option value="draw">Draw / ID</option>
                            <option value="double_loss">Double Loss</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 text-xs text-slate-400">
                        <input type="checkbox" name="is_intentional_draw" value="1" class="rounded">
                        Intentional Draw
                    </label>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-1.5 rounded-lg text-xs font-medium">Submit Result</button>
                </form>
            </div>
            @endif

        </div>
        @endforeach
    </div>

</div>
@endsection
