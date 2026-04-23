@extends('layouts.app')
@section('title', 'Products')

@section('actions')
    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
        + Add Product
    </a>
@endsection

@section('content')
<div class="space-y-4 mt-2">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
               class="bg-slate-800 border border-slate-600 text-slate-300 placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
        <select name="game" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Games</option>
            @foreach($games as $val => $label)
            <option value="{{ $val }}" {{ request('game') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="category" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $val => $label)
            <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        <a href="{{ route('products.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 px-4 py-2 rounded-lg text-sm">Reset</a>
    </form>

    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-4 py-3 font-medium">Product</th>
                        <th class="px-4 py-3 font-medium">Game / Category</th>
                        <th class="px-4 py-3 font-medium text-right">Cost</th>
                        <th class="px-4 py-3 font-medium text-right">MSRP</th>
                        <th class="px-4 py-3 font-medium text-right">Margin</th>
                        <th class="px-4 py-3 font-medium text-right">Stock</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-700/30">
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $product) }}" class="text-white hover:text-indigo-400 font-medium">{{ $product->name }}</a>
                            @if($product->sku)
                            <p class="text-xs text-slate-500 font-mono">{{ $product->sku }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-slate-300 capitalize">{{ $product->game }}</span>
                            <p class="text-xs text-slate-500">{{ \App\Models\Product::categoryLabel($product->category) }}</p>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-300">${{ number_format($product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-right text-slate-300">${{ number_format($product->msrp, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="{{ $product->margin_percent >= 30 ? 'text-emerald-400' : ($product->margin_percent >= 15 ? 'text-amber-400' : 'text-red-400') }} font-medium">
                                {{ $product->margin_percent }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">
                            {{ $product->inventoryItem?->quantity_on_hand ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($product->is_active)
                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-emerald-900/50 text-emerald-300">Active</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-slate-700 text-slate-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('products.show', $product) }}" class="text-xs text-slate-400 hover:text-white">View</a>
                                <a href="{{ route('products.edit', $product) }}" class="text-xs text-indigo-400 hover:text-indigo-300">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-500">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-700">{{ $products->links() }}</div>
    </div>
</div>
@endsection
