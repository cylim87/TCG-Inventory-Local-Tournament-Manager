@extends('layouts.app')
@section('title', 'Margin Report')
@section('breadcrumb', 'Reports › Product Margins')

@section('content')
<div class="space-y-5 mt-2">

    {{-- Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Avg Margin</p>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($summary['avg_margin_percent'], 1) }}%</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Total Carton Margin</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">${{ number_format($summary['total_carton_margin'], 0) }}</p>
        </div>
        @if($summary['best_product'])
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Best Margin</p>
            <p class="text-lg font-bold text-emerald-400 mt-1">{{ $summary['best_product']['margin_percent'] }}%</p>
            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $summary['best_product']['product_name'] }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400">Lowest Margin</p>
            <p class="text-lg font-bold text-amber-400 mt-1">{{ $summary['worst_product']['margin_percent'] }}%</p>
            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $summary['worst_product']['product_name'] }}</p>
        </div>
        @endif
    </div>

    {{-- Margin table --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-white">Product Margin Analysis</h2>
            <p class="text-xs text-slate-400 mt-1">Booster boxes, cartons, and ETBs sorted by margin percentage</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-4 py-3 font-medium">Product</th>
                        <th class="px-4 py-3 font-medium">Game</th>
                        <th class="px-4 py-3 font-medium text-right">Cost/Unit</th>
                        <th class="px-4 py-3 font-medium text-right">MSRP</th>
                        <th class="px-4 py-3 font-medium text-right">Margin/Unit</th>
                        <th class="px-4 py-3 font-medium text-right">Margin %</th>
                        <th class="px-4 py-3 font-medium text-right">Box/Ctn</th>
                        <th class="px-4 py-3 font-medium text-right">Carton Cost</th>
                        <th class="px-4 py-3 font-medium text-right">Carton Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @foreach($analyses->sortByDesc('margin_percent') as $a)
                    <tr class="hover:bg-slate-700/30">
                        <td class="px-4 py-3">
                            <p class="text-white font-medium text-sm">{{ $a['product_name'] }}</p>
                            <p class="text-xs text-slate-500">{{ \App\Models\Product::categoryLabel($a['category']) }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs capitalize">{{ $a['game'] }}</td>
                        <td class="px-4 py-3 text-right text-slate-300 font-mono text-xs">${{ number_format($a['cost_per_unit'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-slate-300 font-mono text-xs">${{ number_format($a['msrp_per_unit'], 2) }}</td>
                        <td class="px-4 py-3 text-right {{ $a['margin_per_unit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-mono text-xs">
                            ${{ number_format($a['margin_per_unit'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold text-sm
                                @if($a['margin_percent'] >= 40) text-emerald-400
                                @elseif($a['margin_percent'] >= 25) text-amber-400
                                @else text-red-400 @endif">
                                {{ $a['margin_percent'] }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-400 text-xs">{{ $a['boxes_per_carton'] }}</td>
                        <td class="px-4 py-3 text-right text-slate-300 font-mono text-xs">${{ number_format($a['carton_cost'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $a['carton_margin'] >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-mono">
                            ${{ number_format($a['carton_margin'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
