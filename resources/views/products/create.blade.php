@extends('layouts.app')
@section('title', 'Add Product')
@section('breadcrumb', 'Products › New')

@section('content')
<div class="max-w-2xl mt-2">
    <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
        @csrf

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Product Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Game</label>
                    <select name="game" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">— Select —</option>
                        @foreach($games as $val => $label)
                        <option value="{{ $val }}" {{ old('game') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Category *</label>
                    <select name="category" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">— Select —</option>
                        @foreach($categories as $val => $label)
                        <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Card Set</label>
                    <select name="card_set_id" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">— None —</option>
                        @foreach($cardSets->groupBy('game') as $game => $sets)
                        <optgroup label="{{ \App\Models\CardSet::gameLabel($game) }}">
                            @foreach($sets as $set)
                            <option value="{{ $set->id }}" {{ old('card_set_id') == $set->id ? 'selected' : '' }}>{{ $set->name }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Pricing</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Cost Price (per unit) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="cost_price" value="{{ old('cost_price', '0.00') }}" step="0.01" min="0" required
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">MSRP *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="msrp" value="{{ old('msrp', '0.00') }}" step="0.01" min="0" required
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Boxes per Carton</label>
                    <input type="number" name="boxes_per_carton" value="{{ old('boxes_per_carton', 6) }}" min="1"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Packs per Box</label>
                    <input type="number" name="packs_per_box" value="{{ old('packs_per_box') }}" min="1"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Cards per Pack</label>
                    <input type="number" name="cards_per_pack" value="{{ old('cards_per_pack') }}" min="1"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Inventory Settings</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Reorder Point (alert when ≤)</label>
                    <input type="number" name="reorder_point" value="{{ old('reorder_point', 5) }}" min="0"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Reorder Quantity</label>
                    <input type="number" name="reorder_quantity" value="{{ old('reorder_quantity', 10) }}" min="1"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Create Product</button>
            <a href="{{ route('products.index') }}" class="text-slate-400 hover:text-slate-300 text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
