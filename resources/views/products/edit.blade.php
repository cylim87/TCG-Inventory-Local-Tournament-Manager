@extends('layouts.app')
@section('title', 'Edit: ' . $product->name)

@section('content')
<div class="max-w-2xl mt-2">
    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Product Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Game</label>
                    <select name="game" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">— Select —</option>
                        @foreach($games as $val => $label)
                        <option value="{{ $val }}" {{ old('game', $product->game) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Category *</label>
                    <select name="category" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        @foreach($categories as $val => $label)
                        <option value="{{ $val }}" {{ old('category', $product->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Active</label>
                    <select name="is_active" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !old('is_active', $product->is_active) ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Pricing</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Cost Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0" required
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">MSRP *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="msrp" value="{{ old('msrp', $product->msrp) }}" step="0.01" min="0" required
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Boxes per Carton</label>
                    <input type="number" name="boxes_per_carton" value="{{ old('boxes_per_carton', $product->boxes_per_carton) }}" min="1"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Save Changes</button>
            <a href="{{ route('products.show', $product) }}" class="text-slate-400 hover:text-slate-300 text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
