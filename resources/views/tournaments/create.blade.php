@extends('layouts.app')
@section('title', 'New Tournament')

@section('content')
<div class="max-w-2xl mt-2">
    <form method="POST" action="{{ route('tournaments.store') }}" class="space-y-6">
        @csrf

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Event Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Event Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Weekly Pokémon Standard #42"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Game *</label>
                    <select name="game" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">— Select —</option>
                        @foreach($games as $val => $label)
                        <option value="{{ $val }}" {{ old('game') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Format *</label>
                    <select name="format" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">— Select —</option>
                        @foreach($formats as $val => $label)
                        <option value="{{ $val }}" {{ old('format') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Start Time *</label>
                    <input type="time" name="start_time" value="{{ old('start_time', '18:00') }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Entry Fee</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="entry_fee" value="{{ old('entry_fee', '0.00') }}" step="0.50" min="0"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Prize Pool</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="prize_pool" value="{{ old('prize_pool', '0.00') }}" step="0.50" min="0"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Max Players</label>
                    <input type="number" name="max_players" value="{{ old('max_players') }}" min="2" placeholder="Unlimited"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Top Cut</label>
                    <select name="top_cut" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="0">None (Swiss only)</option>
                        <option value="2">Top 2</option>
                        <option value="4">Top 4</option>
                        <option value="8">Top 8</option>
                        <option value="16">Top 16</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Create Event</button>
            <a href="{{ route('tournaments.index') }}" class="text-slate-400 hover:text-slate-300 text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
