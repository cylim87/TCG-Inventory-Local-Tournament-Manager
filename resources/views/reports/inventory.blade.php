@extends('layouts.app')
@section('title', 'Inventory Report')
@section('breadcrumb', 'Reports › Inventory Valuation')

@section('content')
<div class="space-y-5 mt-2">

    {{-- Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Total Cost Value</p>
            <p class="text-2xl font-bold text-white mt-1">${{ number_format($summary['cost_value'], 2) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Total Retail Value</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">${{ number_format($summary['retail_value'], 2) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Unrealised Margin</p>
            @php $unrealised = $summary['retail_value'] - $summary['cost_value']; @endphp
            <p class="text-2xl font-bold text-indigo-400 mt-1">${{ number_format($unrealised, 2) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Total SKUs</p>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($summary['total_skus']) }}</p>
            <p class="text-xs text-slate-500">{{ number_format($summary['total_units']) }} units</p>
        </div>
    </div>

    {{-- By Game breakdown --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-white">Value by Game</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-5 py-3 font-medium">Game</th>
                        <th class="px-5 py-3 font-medium text-right">SKUs</th>
                        <th class="px-5 py-3 font-medium text-right">Units</th>
                        <th class="px-5 py-3 font-medium text-right">Cost Value</th>
                        <th class="px-5 py-3 font-medium text-right">Retail Value</th>
                        <th class="px-5 py-3 font-medium text-right">Margin</th>
                        <th class="px-5 py-3 font-medium text-right">% of Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach($byGame->sortByDesc('retail_value') as $game => $data)
                    <tr class="hover:bg-slate-700/30">
                        <td class="px-5 py-3 font-medium text-white capitalize">{{ \App\Models\CardSet::gameLabel($game) }}</td>
                        <td class="px-5 py-3 text-right text-slate-400">{{ $data['skus'] }}</td>
                        <td class="px-5 py-3 text-right text-slate-400">{{ number_format($data['units']) }}</td>
                        <td class="px-5 py-3 text-right text-slate-300 font-mono">${{ number_format($data['cost_value'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-slate-300 font-mono">${{ number_format($data['retail_value'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-emerald-400 font-mono">${{ number_format($data['retail_value'] - $data['cost_value'], 2) }}</td>
                        <td class="px-5 py-3 text-right">
                            @php $pct = $summary['retail_value'] > 0 ? round($data['retail_value'] / $summary['retail_value'] * 100, 1) : 0; @endphp
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-16 bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <span class="text-slate-400 text-xs w-10 text-right">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-slate-600 bg-slate-900/30 font-bold">
                    <tr>
                        <td class="px-5 py-3 text-white">Total</td>
                        <td class="px-5 py-3 text-right text-slate-300">{{ $summary['total_skus'] }}</td>
                        <td class="px-5 py-3 text-right text-slate-300">{{ number_format($summary['total_units']) }}</td>
                        <td class="px-5 py-3 text-right text-white font-mono">${{ number_format($summary['cost_value'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-white font-mono">${{ number_format($summary['retail_value'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-emerald-400 font-mono">${{ number_format($unrealised, 2) }}</td>
                        <td class="px-5 py-3 text-right text-slate-400">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Full item list --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-white">All Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-4 py-3 font-medium">Product</th>
                        <th class="px-4 py-3 font-medium">Game</th>
                        <th class="px-4 py-3 font-medium text-right">Units</th>
                        <th class="px-4 py-3 font-medium text-right">Avg Cost</th>
                        <th class="px-4 py-3 font-medium text-right">MSRP</th>
                        <th class="px-4 py-3 font-medium text-right">Cost Value</th>
                        <th class="px-4 py-3 font-medium text-right">Retail Value</th>
                        <th class="px-4 py-3 font-medium text-right">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach($items->sortByDesc(fn($i) => $i->quantity_on_hand * $i->product->msrp) as $item)
                    @if($item->quantity_on_hand > 0)
                    <tr class="hover:bg-slate-700/30">
                        <td class="px-4 py-2.5">
                            <a href="{{ route('products.show', $item->product) }}" class="text-white hover:text-indigo-400">{{ $item->product->name }}</a>
                        </td>
                        <td class="px-4 py-2.5 text-slate-400 text-xs capitalize">{{ $item->product->game }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-slate-300">{{ $item->quantity_on_hand }}</td>
                        <td class="px-4 py-2.5 text-right text-slate-400 font-mono text-xs">${{ number_format($item->average_cost, 2) }}</td>
                        <td class="px-4 py-2.5 text-right text-slate-400 font-mono text-xs">${{ number_format($item->product->msrp, 2) }}</td>
                        <td class="px-4 py-2.5 text-right text-slate-300 font-mono text-xs">${{ number_format($item->quantity_on_hand * $item->average_cost, 2) }}</td>
                        <td class="px-4 py-2.5 text-right text-white font-mono text-xs font-medium">${{ number_format($item->quantity_on_hand * $item->product->msrp, 2) }}</td>
                        <td class="px-4 py-2.5 text-right text-emerald-400 font-mono text-xs">
                            ${{ number_format(($item->product->msrp - $item->average_cost) * $item->quantity_on_hand, 2) }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
