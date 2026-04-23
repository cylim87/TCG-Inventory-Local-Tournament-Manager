@extends('layouts.app')
@section('title', 'Stock Levels')
@section('breadcrumb', 'Inventory › Stock Levels')

@section('actions')
    <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
        + Purchase Order
    </a>
@endsection

@section('content')
<div class="space-y-5 mt-2">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Cost Value</p>
            <p class="text-xl font-bold text-white mt-1">${{ number_format($summary['cost_value'], 0) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Retail Value</p>
            <p class="text-xl font-bold text-emerald-400 mt-1">${{ number_format($summary['retail_value'], 0) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Low Stock SKUs</p>
            <p class="text-xl font-bold text-amber-400 mt-1">{{ $summary['low_stock_count'] }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Out of Stock</p>
            <p class="text-xl font-bold text-red-400 mt-1">{{ $summary['out_of_stock_count'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
               class="bg-slate-800 border border-slate-600 text-slate-300 placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
        <select name="game" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
            <option value="">All Games</option>
            @foreach(['pokemon' => 'Pokémon', 'mtg' => 'MTG', 'yugioh' => 'Yu-Gi-Oh!', 'one_piece' => 'One Piece', 'lorcana' => 'Lorcana', 'fab' => 'F&B', 'digimon' => 'Digimon'] as $val => $label)
            <option value="{{ $val }}" {{ request('game') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
            <option value="">All Status</option>
            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
            <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
        </select>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        <a href="{{ route('inventory.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 px-4 py-2 rounded-lg text-sm">Reset</a>
    </form>

    {{-- Table --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700 bg-slate-800/80">
                        <th class="px-4 py-3 font-medium">Product</th>
                        <th class="px-4 py-3 font-medium">Game</th>
                        <th class="px-4 py-3 font-medium text-right">On Hand</th>
                        <th class="px-4 py-3 font-medium text-right">Reorder At</th>
                        <th class="px-4 py-3 font-medium text-right">Avg Cost</th>
                        <th class="px-4 py-3 font-medium text-right">MSRP</th>
                        <th class="px-4 py-3 font-medium text-right">Stock Value</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-700/30 transition-colors" x-data="{ adjusting: false }">
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $item->product) }}" class="text-white hover:text-indigo-400 font-medium">
                                {{ $item->product->name }}
                            </a>
                            @if($item->product->sku)
                            <p class="text-xs text-slate-500 font-mono">{{ $item->product->sku }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs uppercase">{{ $item->product->game }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold
                            {{ $item->is_out_of_stock ? 'text-red-400' : ($item->is_low_stock ? 'text-amber-400' : 'text-emerald-400') }}">
                            {{ $item->quantity_on_hand }}
                        </td>
                        <td class="px-4 py-3 text-right text-slate-400 font-mono">{{ $item->reorder_point }}</td>
                        <td class="px-4 py-3 text-right text-slate-300">${{ number_format($item->average_cost, 2) }}</td>
                        <td class="px-4 py-3 text-right text-slate-300">${{ number_format($item->product->msrp, 2) }}</td>
                        <td class="px-4 py-3 text-right text-white font-medium">${{ number_format($item->stock_value, 2) }}</td>
                        <td class="px-4 py-3">
                            @if($item->is_out_of_stock)
                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-red-900/50 text-red-300">Out of Stock</span>
                            @elseif($item->is_low_stock)
                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-amber-900/50 text-amber-300">Low Stock</span>
                            @else
                            <span class="inline-block px-2 py-0.5 rounded text-xs bg-emerald-900/50 text-emerald-300">In Stock</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button @click="adjusting = !adjusting" class="text-xs text-indigo-400 hover:text-indigo-300">Adjust</button>
                        </td>
                    </tr>
                    {{-- Inline adjust row --}}
                    <tr x-show="adjusting" x-cloak class="bg-slate-900/50">
                        <td colspan="9" class="px-4 py-3">
                            <form method="POST" action="{{ route('inventory.adjust', $item->product) }}" class="flex items-center gap-3">
                                @csrf
                                <label class="text-xs text-slate-400">New quantity:</label>
                                <input type="number" name="new_quantity" value="{{ $item->quantity_on_hand }}" min="0"
                                       class="w-24 bg-slate-700 border border-slate-600 text-white rounded px-2 py-1 text-sm">
                                <input type="text" name="notes" placeholder="Reason (optional)"
                                       class="flex-1 max-w-xs bg-slate-700 border border-slate-600 text-white placeholder-slate-500 rounded px-2 py-1 text-sm">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs">Save</button>
                                <button type="button" @click="adjusting = false" class="text-slate-400 hover:text-slate-300 text-xs">Cancel</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-500">No inventory items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-700">
            {{ $items->links() }}
        </div>
    </div>

</div>
@endsection
