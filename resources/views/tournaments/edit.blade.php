@extends('layouts.app')
@section('title', 'Edit: ' . $tournament->name)

@section('content')
<div class="max-w-2xl mt-2">
    <form method="POST" action="{{ route('tournaments.update', $tournament) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Edit Event</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Event Name *</label>
                    <input type="text" name="name" value="{{ old('name', $tournament->name) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Game *</label>
                    <select name="game" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        @foreach($games as $val => $label)
                        <option value="{{ $val }}" {{ old('game', $tournament->game) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Format *</label>
                    <select name="format" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        @foreach($formats as $val => $label)
                        <option value="{{ $val }}" {{ old('format', $tournament->format) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ old('date', $tournament->date->format('Y-m-d')) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Start Time *</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $tournament->start_time) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Entry Fee</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="entry_fee" value="{{ old('entry_fee', $tournament->entry_fee) }}" step="0.50" min="0"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Prize Pool</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="prize_pool" value="{{ old('prize_pool', $tournament->prize_pool) }}" step="0.50" min="0"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Number of Rounds</label>
                    <input type="number" name="rounds" value="{{ old('rounds', $tournament->rounds) }}" min="1" max="15"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">{{ old('description', $tournament->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Save Changes</button>
            <a href="{{ route('tournaments.show', $tournament) }}" class="text-slate-400 hover:text-slate-300 text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
