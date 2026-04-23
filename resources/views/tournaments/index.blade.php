@extends('layouts.app')
@section('title', 'Tournaments')

@section('actions')
    <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
        + New Event
    </a>
@endsection

@section('content')
<div class="space-y-4 mt-2">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="game" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Games</option>
            @foreach(['pokemon' => 'Pokémon', 'mtg' => 'MTG', 'yugioh' => 'Yu-Gi-Oh!', 'one_piece' => 'One Piece', 'lorcana' => 'Lorcana', 'fab' => 'F&B', 'digimon' => 'Digimon'] as $val => $label)
            <option value="{{ $val }}" {{ request('game') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Status</option>
            @foreach(['registration' => 'Registration', 'active' => 'Active', 'top_cut' => 'Top Cut', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($tournaments as $t)
        <a href="{{ route('tournaments.show', $t) }}"
           class="bg-slate-800 border border-slate-700 hover:border-indigo-600 rounded-xl p-5 transition-colors block">
            <div class="flex items-start justify-between gap-2 mb-3">
                <div>
                    <h3 class="font-semibold text-white">{{ $t->name }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $t->date->format('M j, Y') }} · {{ $t->start_time }}</p>
                </div>
                <span class="inline-block px-2 py-0.5 rounded text-xs shrink-0
                    @if($t->status === 'registration') bg-blue-900/50 text-blue-300
                    @elseif($t->status === 'active') bg-green-900/50 text-green-300
                    @elseif($t->status === 'top_cut') bg-yellow-900/50 text-yellow-300
                    @elseif($t->status === 'completed') bg-slate-700 text-slate-400
                    @else bg-red-900/50 text-red-300 @endif">
                    {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-400">
                <span class="capitalize">{{ $t->game }}</span>
                <span>{{ \App\Models\Tournament::formatLabel($t->format) }}</span>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-700 text-sm">
                <span class="text-slate-400">{{ $t->registrations_count }} players</span>
                <span class="text-slate-300 font-medium">
                    @if($t->entry_fee > 0) ${{ number_format($t->entry_fee, 2) }} entry @else Free @endif
                </span>
            </div>
        </a>
        @empty
        <div class="col-span-3 text-center py-16 text-slate-500">
            No tournaments found. <a href="{{ route('tournaments.create') }}" class="text-indigo-400 hover:text-indigo-300">Create one →</a>
        </div>
        @endforelse
    </div>

    <div>{{ $tournaments->links() }}</div>
</div>
@endsection
