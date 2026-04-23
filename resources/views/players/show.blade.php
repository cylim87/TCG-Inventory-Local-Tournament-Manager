@extends('layouts.app')
@section('title', $player->full_name)

@section('actions')
    <a href="{{ route('players.edit', $player) }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-sm">Edit</a>
@endsection

@section('content')
<div class="space-y-5 mt-2">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-lg font-bold text-white">
                    {{ strtoupper(substr($player->first_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">{{ $player->full_name }}</h2>
                    @if($player->player_number)
                    <p class="text-xs text-slate-400 font-mono">ID: {{ $player->player_number }}</p>
                    @endif
                </div>
            </div>
            <div class="space-y-2 text-sm">
                @if($player->email)
                <div class="flex justify-between"><span class="text-slate-400">Email</span><span class="text-slate-300">{{ $player->email }}</span></div>
                @endif
                @if($player->phone)
                <div class="flex justify-between"><span class="text-slate-400">Phone</span><span class="text-slate-300">{{ $player->phone }}</span></div>
                @endif
                @if($player->date_of_birth)
                <div class="flex justify-between"><span class="text-slate-400">DOB</span><span class="text-slate-300">{{ $player->date_of_birth->format('M j, Y') }}</span></div>
                @endif
                @if($player->preferred_game)
                <div class="flex justify-between"><span class="text-slate-400">Main Game</span><span class="text-slate-300 capitalize">{{ $player->preferred_game }}</span></div>
                @endif
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                <h3 class="font-semibold text-white mb-4">Tournament History</h3>
                @if($history->isEmpty())
                <p class="text-sm text-slate-500">No tournament history yet.</p>
                @else
                <div class="space-y-2">
                    @foreach($history->take(10) as $reg)
                    <div class="flex items-center gap-3 py-2 border-b border-slate-700 last:border-0">
                        <div class="flex-1">
                            <a href="{{ route('tournaments.show', $reg->tournament) }}" class="text-sm text-white hover:text-indigo-400">
                                {{ $reg->tournament->name }}
                            </a>
                            <p class="text-xs text-slate-400">{{ $reg->tournament->date->format('M j, Y') }} · {{ ucfirst($reg->tournament->game) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-0.5 rounded text-xs
                                @if($reg->tournament->status === 'completed') bg-slate-700 text-slate-300
                                @elseif($reg->tournament->status === 'active') bg-green-900/50 text-green-300
                                @else bg-blue-900/50 text-blue-300 @endif">
                                {{ ucfirst($reg->tournament->status) }}
                            </span>
                            @if($reg->dropped)
                            <p class="text-xs text-red-400 mt-0.5">Dropped R{{ $reg->drop_round }}</p>
                            @endif
                        </div>
                        @if($reg->paid)
                        <span class="text-xs text-emerald-400">✓ Paid</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
