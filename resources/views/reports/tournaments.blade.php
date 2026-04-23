@extends('layouts.app')
@section('title', 'Tournament Report')
@section('breadcrumb', 'Reports › Tournament History')

@section('content')
<div class="space-y-5 mt-2">

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Total Events</p>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($summary['total_events']) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Total Players</p>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($summary['total_players']) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Total Revenue</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">${{ number_format($summary['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Avg Players / Event</p>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($summary['avg_players'], 1) }}</p>
        </div>
    </div>

    {{-- By game breakdown --}}
    @if($summary['by_game']->isNotEmpty())
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-white">Revenue by Game</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-5 py-3 font-medium">Game</th>
                        <th class="px-5 py-3 font-medium text-right">Events</th>
                        <th class="px-5 py-3 font-medium text-right">Players</th>
                        <th class="px-5 py-3 font-medium text-right">Revenue</th>
                        <th class="px-5 py-3 font-medium text-right">Avg Players</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach($summary['by_game']->sortByDesc('revenue') as $game => $data)
                    <tr class="hover:bg-slate-700/30">
                        <td class="px-5 py-3 font-medium text-white">{{ \App\Models\CardSet::gameLabel($game) }}</td>
                        <td class="px-5 py-3 text-right text-slate-300">{{ $data['count'] }}</td>
                        <td class="px-5 py-3 text-right text-slate-300">{{ number_format($data['total_players']) }}</td>
                        <td class="px-5 py-3 text-right text-emerald-400 font-mono">${{ number_format($data['revenue'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-slate-400">{{ $data['count'] > 0 ? number_format($data['total_players'] / $data['count'], 1) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Event list --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-white">Completed Events</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-5 py-3 font-medium">Event</th>
                        <th class="px-5 py-3 font-medium">Game / Format</th>
                        <th class="px-5 py-3 font-medium">Date</th>
                        <th class="px-5 py-3 font-medium text-right">Players</th>
                        <th class="px-5 py-3 font-medium text-right">Entry Fee</th>
                        <th class="px-5 py-3 font-medium text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($tournaments as $t)
                    <tr class="hover:bg-slate-700/30">
                        <td class="px-5 py-3">
                            <a href="{{ route('tournaments.show', $t) }}" class="text-white hover:text-indigo-400 font-medium">
                                {{ $t->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-slate-300 text-xs capitalize">{{ $t->game }}</span>
                            <p class="text-slate-500 text-xs">{{ \App\Models\Tournament::formatLabel($t->format) }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-400">{{ $t->date->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-right text-slate-300">{{ $t->registrations_count }}</td>
                        <td class="px-5 py-3 text-right text-slate-400">${{ number_format($t->entry_fee, 2) }}</td>
                        <td class="px-5 py-3 text-right text-emerald-400 font-medium font-mono">
                            ${{ number_format($t->registrations_count * $t->entry_fee, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No completed tournaments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
