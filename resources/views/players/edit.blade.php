@extends('layouts.app')
@section('title', 'Edit Player')

@section('content')
<div class="max-w-xl mt-2">
    <form method="POST" action="{{ route('players.update', $player) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Edit: {{ $player->full_name }}</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $player->first_name) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $player->last_name) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $player->email) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $player->phone) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Player # (DCI/ID)</label>
                    <input type="text" name="player_number" value="{{ old('player_number', $player->player_number) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Preferred Game</label>
                    <select name="preferred_game" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">— None —</option>
                        @foreach(['pokemon' => 'Pokémon', 'mtg' => 'MTG', 'yugioh' => 'Yu-Gi-Oh!', 'one_piece' => 'One Piece', 'lorcana' => 'Lorcana', 'fab' => 'Flesh and Blood', 'digimon' => 'Digimon'] as $val => $label)
                        <option value="{{ $val }}" {{ old('preferred_game', $player->preferred_game) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Save Changes</button>
            <a href="{{ route('players.show', $player) }}" class="text-slate-400 hover:text-slate-300 text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
