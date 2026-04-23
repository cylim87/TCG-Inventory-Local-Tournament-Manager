@extends('layouts.app')
@section('title', 'Players')

@section('actions')
    <a href="{{ route('players.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
        + Register Player
    </a>
@endsection

@section('content')
<div class="space-y-4 mt-2">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, or player #..."
               class="bg-slate-800 border border-slate-600 text-slate-300 placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 w-64">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Search</button>
        <a href="{{ route('players.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 px-4 py-2 rounded-lg text-sm">Reset</a>
    </form>

    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                    <th class="px-5 py-3 font-medium">Player</th>
                    <th class="px-5 py-3 font-medium">Player #</th>
                    <th class="px-5 py-3 font-medium">Contact</th>
                    <th class="px-5 py-3 font-medium">Preferred Game</th>
                    <th class="px-5 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($players as $player)
                <tr class="hover:bg-slate-700/30">
                    <td class="px-5 py-3">
                        <a href="{{ route('players.show', $player) }}" class="font-medium text-white hover:text-indigo-400">
                            {{ $player->full_name }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-slate-400 font-mono text-xs">{{ $player->player_number ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <p class="text-slate-400 text-xs">{{ $player->email ?? '—' }}</p>
                        @if($player->phone)<p class="text-slate-500 text-xs">{{ $player->phone }}</p>@endif
                    </td>
                    <td class="px-5 py-3 text-slate-400 text-xs capitalize">{{ $player->preferred_game ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('players.show', $player) }}" class="text-xs text-slate-400 hover:text-white">View</a>
                            <a href="{{ route('players.edit', $player) }}" class="text-xs text-indigo-400 hover:text-indigo-300">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No players found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-slate-700">{{ $players->links() }}</div>
    </div>
</div>
@endsection
