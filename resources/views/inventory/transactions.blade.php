@extends('layouts.app')
@section('title', 'Stock History: ' . $product->name)
@section('breadcrumb', 'Inventory › ' . $product->name . ' › Transactions')

@section('actions')
    <a href="{{ route('inventory.index') }}" class="text-slate-400 hover:text-white text-sm">← Stock Levels</a>
    <a href="{{ route('products.show', $product) }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-sm">Product Detail</a>
@endsection

@section('content')
<div class="space-y-4 mt-2">

    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center gap-4">
        <div>
            <h2 class="font-semibold text-white">{{ $product->name }}</h2>
            <p class="text-xs text-slate-400">{{ \App\Models\Product::categoryLabel($product->category) }} · {{ $product->game }}</p>
        </div>
        <div class="ml-auto flex gap-6 text-center">
            <div>
                <p class="text-lg font-bold text-white">{{ $product->inventoryItem?->quantity_on_hand ?? 0 }}</p>
                <p class="text-xs text-slate-400">On Hand</p>
            </div>
            <div>
                <p class="text-lg font-bold text-white">${{ number_format($product->inventoryItem?->average_cost ?? 0, 2) }}</p>
                <p class="text-xs text-slate-400">Avg Cost</p>
            </div>
        </div>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-5 py-3 font-medium">Date</th>
                        <th class="px-5 py-3 font-medium">Type</th>
                        <th class="px-5 py-3 font-medium text-right">Qty</th>
                        <th class="px-5 py-3 font-medium text-right">Unit Cost</th>
                        <th class="px-5 py-3 font-medium">Notes</th>
                        <th class="px-5 py-3 font-medium">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($transactions as $txn)
                    <tr class="hover:bg-slate-700/30">
                        <td class="px-5 py-3 text-slate-400 text-xs whitespace-nowrap">{{ $txn->created_at->format('M j, Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                @if($txn->type === 'purchase') bg-emerald-900/50 text-emerald-300
                                @elseif($txn->type === 'sale') bg-blue-900/50 text-blue-300
                                @elseif($txn->type === 'damage') bg-red-900/50 text-red-300
                                @elseif($txn->type === 'adjustment' || $txn->type === 'count') bg-yellow-900/50 text-yellow-300
                                @elseif($txn->type === 'return') bg-indigo-900/50 text-indigo-300
                                @else bg-slate-700 text-slate-400 @endif">
                                {{ ucfirst($txn->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-mono font-bold {{ $txn->quantity >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $txn->quantity >= 0 ? '+' : '' }}{{ $txn->quantity }}
                        </td>
                        <td class="px-5 py-3 text-right text-slate-400 font-mono text-xs">
                            {{ $txn->unit_cost ? '$' . number_format($txn->unit_cost, 2) : '—' }}
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs max-w-xs truncate">{{ $txn->notes ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $txn->user?->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No transactions recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-700">{{ $transactions->links() }}</div>
    </div>

</div>
@endsection
