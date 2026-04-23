@extends('layouts.app')
@section('title', $product->name)
@section('breadcrumb', 'Products › ' . $product->name)

@section('actions')
    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
        Edit
    </a>
@endsection

@section('content')
<div class="space-y-6 mt-2">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Product Info --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $product->name }}</h2>
                        <p class="text-sm text-slate-400 mt-1">
                            {{ \App\Models\CardSet::gameLabel($product->game ?? 'other') }} ·
                            {{ \App\Models\Product::categoryLabel($product->category) }}
                            @if($product->cardSet) · {{ $product->cardSet->name }} @endif
                        </p>
                    </div>
                    <span class="{{ $product->is_active ? 'bg-emerald-900/50 text-emerald-300' : 'bg-slate-700 text-slate-400' }} px-2 py-1 rounded text-xs">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                @if($product->description)
                <p class="text-sm text-slate-400 mt-3">{{ $product->description }}</p>
                @endif

                <div class="grid grid-cols-3 gap-4 mt-5">
                    <div class="bg-slate-900/50 rounded-lg p-3">
                        <p class="text-xs text-slate-400">Cost Price</p>
                        <p class="text-lg font-bold text-white">${{ number_format($product->cost_price, 2) }}</p>
                        <p class="text-xs text-slate-500">per {{ $product->category === 'carton' ? 'carton' : 'unit' }}</p>
                    </div>
                    <div class="bg-slate-900/50 rounded-lg p-3">
                        <p class="text-xs text-slate-400">MSRP</p>
                        <p class="text-lg font-bold text-white">${{ number_format($product->msrp, 2) }}</p>
                        <p class="text-xs text-slate-500">per {{ $product->category === 'carton' ? 'carton' : 'unit' }}</p>
                    </div>
                    <div class="bg-slate-900/50 rounded-lg p-3">
                        <p class="text-xs text-slate-400">Margin</p>
                        <p class="text-lg font-bold {{ $product->margin_percent >= 30 ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $product->margin_percent }}%
                        </p>
                        <p class="text-xs text-slate-500">${{ number_format($product->margin, 2) }} per unit</p>
                    </div>
                </div>
            </div>

            {{-- Margin Analysis --}}
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                <h3 class="font-semibold text-white mb-4">Carton Margin Analysis</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                    <div>
                        <p class="text-xs text-slate-400">Boxes / Carton</p>
                        <p class="text-xl font-bold text-white">{{ $analysis['boxes_per_carton'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Carton Cost</p>
                        <p class="text-xl font-bold text-white">${{ number_format($analysis['carton_cost'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Carton MSRP</p>
                        <p class="text-xl font-bold text-white">${{ number_format($analysis['carton_msrp'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Carton Margin</p>
                        <p class="text-xl font-bold text-emerald-400">${{ number_format($analysis['carton_margin'], 2) }}</p>
                    </div>
                </div>

                {{-- Price scenario table --}}
                <h4 class="text-sm font-medium text-slate-300 mb-2">Price Scenarios (per carton of {{ $analysis['boxes_per_carton'] }})</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left text-slate-400 border-b border-slate-700">
                                <th class="pb-2 font-medium">Sell Price</th>
                                <th class="pb-2 font-medium text-right">Margin/Unit</th>
                                <th class="pb-2 font-medium text-right">Margin %</th>
                                <th class="pb-2 font-medium text-right">Total Revenue</th>
                                <th class="pb-2 font-medium text-right">Total Margin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @foreach($scenarios as $s)
                            <tr class="{{ !$s['profitable'] ? 'opacity-50' : '' }}">
                                <td class="py-2 text-white font-medium">${{ number_format($s['price'], 2) }}
                                    @if($s['label'] === 'msrp') <span class="text-slate-500">(MSRP)</span> @endif
                                </td>
                                <td class="py-2 text-right {{ $s['profitable'] ? 'text-emerald-400' : 'text-red-400' }}">
                                    ${{ number_format($s['margin_per_unit'], 2) }}
                                </td>
                                <td class="py-2 text-right {{ $s['profitable'] ? 'text-emerald-400' : 'text-red-400' }}">
                                    {{ $s['margin_percent'] }}%
                                </td>
                                <td class="py-2 text-right text-slate-300">${{ number_format($s['total_revenue'], 2) }}</td>
                                <td class="py-2 text-right {{ $s['profitable'] ? 'text-emerald-400' : 'text-red-400' }}">
                                    ${{ number_format($s['total_margin'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($analysis['packs_per_box'])
                <div class="mt-4 p-3 bg-slate-900/50 rounded-lg text-xs text-slate-400">
                    Pack breakdown: {{ $analysis['packs_per_box'] }} packs/box ·
                    Cost/pack: ${{ number_format($analysis['cost_per_pack'], 4) }} ·
                    Sell/pack: ${{ number_format($analysis['sell_per_pack'], 4) }}
                </div>
                @endif
            </div>
        </div>

        {{-- Side panel --}}
        <div class="space-y-5">
            {{-- Stock --}}
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                <h3 class="font-semibold text-white mb-3">Stock</h3>
                @if($product->inventoryItem)
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-400">On Hand</span>
                        <span class="font-bold text-white">{{ $product->inventoryItem->quantity_on_hand }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-400">Reorder Point</span>
                        <span class="text-slate-300">{{ $product->inventoryItem->reorder_point }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-400">Stock Value</span>
                        <span class="text-emerald-400 font-medium">${{ number_format($product->inventoryItem->stock_value, 2) }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-700">
                        <span class="inline-block px-2 py-1 rounded text-xs
                            @if($product->inventoryItem->is_out_of_stock) bg-red-900/50 text-red-300
                            @elseif($product->inventoryItem->is_low_stock) bg-amber-900/50 text-amber-300
                            @else bg-emerald-900/50 text-emerald-300
                            @endif">
                            @if($product->inventoryItem->is_out_of_stock) Out of Stock
                            @elseif($product->inventoryItem->is_low_stock) Low Stock
                            @else In Stock
                            @endif
                        </span>
                    </div>
                </div>
                @else
                <p class="text-sm text-slate-500">No inventory record.</p>
                @endif
                <div class="mt-4 pt-3 border-t border-slate-700">
                    <a href="{{ route('inventory.transactions', $product) }}" class="text-xs text-indigo-400 hover:text-indigo-300">View transaction history →</a>
                </div>
            </div>

            {{-- Meta --}}
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 text-sm space-y-2">
                @if($product->sku) <div class="flex justify-between"><span class="text-slate-400">SKU</span><span class="font-mono text-slate-300">{{ $product->sku }}</span></div> @endif
                @if($product->barcode) <div class="flex justify-between"><span class="text-slate-400">Barcode</span><span class="font-mono text-slate-300">{{ $product->barcode }}</span></div> @endif
                @if($product->packs_per_box) <div class="flex justify-between"><span class="text-slate-400">Packs/Box</span><span class="text-slate-300">{{ $product->packs_per_box }}</span></div> @endif
                @if($product->cards_per_pack) <div class="flex justify-between"><span class="text-slate-400">Cards/Pack</span><span class="text-slate-300">{{ $product->cards_per_pack }}</span></div> @endif
            </div>
        </div>

    </div>
</div>
@endsection
