@extends('layouts.app')
@section('title', $purchaseOrder->po_number)
@section('breadcrumb', 'Purchase Orders › ' . $purchaseOrder->po_number)

@section('actions')
    @if($purchaseOrder->status === 'draft')
    <form method="POST" action="{{ route('purchase-orders.mark-ordered', $purchaseOrder) }}" class="inline">
        @csrf
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm">Mark as Ordered</button>
    </form>
    @endif
    @if(in_array($purchaseOrder->status, ['ordered', 'partial']))
    <button onclick="document.getElementById('receive-form').classList.toggle('hidden')"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm">Receive Items</button>
    @endif
    @if($purchaseOrder->status !== 'received' && $purchaseOrder->status !== 'cancelled')
    <form method="POST" action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" class="inline"
          onsubmit="return confirm('Cancel this order?')">
        @csrf
        <button class="bg-red-900/50 hover:bg-red-800/50 text-red-400 px-3 py-1.5 rounded-lg text-sm">Cancel</button>
    </form>
    @endif
@endsection

@section('content')
<div class="space-y-6 mt-2">

    {{-- Header --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
            <p class="text-xs text-slate-400 mb-1">Supplier</p>
            <p class="font-semibold text-white">{{ $purchaseOrder->supplier->name }}</p>
            @if($purchaseOrder->supplier->contact_name)
            <p class="text-xs text-slate-400 mt-1">{{ $purchaseOrder->supplier->contact_name }}</p>
            @endif
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
            <p class="text-xs text-slate-400 mb-1">Status</p>
            <span class="inline-block px-3 py-1 rounded-lg text-sm font-medium
                @if($purchaseOrder->status === 'received') bg-emerald-900/50 text-emerald-300
                @elseif($purchaseOrder->status === 'ordered') bg-blue-900/50 text-blue-300
                @elseif($purchaseOrder->status === 'partial') bg-amber-900/50 text-amber-300
                @elseif($purchaseOrder->status === 'cancelled') bg-red-900/50 text-red-300
                @else bg-slate-700 text-slate-400 @endif">
                {{ ucfirst($purchaseOrder->status) }}
            </span>
            <p class="text-xs text-slate-400 mt-2">Ordered: {{ $purchaseOrder->order_date->format('M j, Y') }}</p>
            @if($purchaseOrder->expected_date)
            <p class="text-xs text-slate-400">Expected: {{ $purchaseOrder->expected_date->format('M j, Y') }}</p>
            @endif
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
            <p class="text-xs text-slate-400 mb-1">Order Total</p>
            <p class="text-2xl font-bold text-white">${{ number_format($purchaseOrder->total_amount, 2) }}</p>
            <p class="text-xs text-slate-500 mt-1">Placed by {{ $purchaseOrder->user->name }}</p>
        </div>
    </div>

    {{-- Line items --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-white">Order Items</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                    <th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 font-medium text-right">Unit Cost</th>
                    <th class="px-5 py-3 font-medium text-right">MSRP</th>
                    <th class="px-5 py-3 font-medium text-right">Margin</th>
                    <th class="px-5 py-3 font-medium text-right">Ordered</th>
                    <th class="px-5 py-3 font-medium text-right">Received</th>
                    <th class="px-5 py-3 font-medium text-right">Line Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @foreach($purchaseOrder->items as $item)
                <tr>
                    <td class="px-5 py-3">
                        <a href="{{ route('products.show', $item->product) }}" class="text-white hover:text-indigo-400">{{ $item->product->name }}</a>
                        <p class="text-xs text-slate-500">{{ $item->product->boxes_per_carton }} boxes/carton</p>
                    </td>
                    <td class="px-5 py-3 text-right text-slate-300 font-mono">${{ number_format($item->unit_cost, 2) }}</td>
                    <td class="px-5 py-3 text-right text-slate-400 font-mono">${{ number_format($item->product->msrp, 2) }}</td>
                    <td class="px-5 py-3 text-right">
                        <span class="{{ $item->box_margin_percent >= 30 ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $item->box_margin_percent }}%
                        </span>
                        <p class="text-xs text-slate-500">${{ number_format($item->box_margin, 2) }}/unit</p>
                    </td>
                    <td class="px-5 py-3 text-right font-mono text-slate-300">{{ $item->quantity_ordered }}</td>
                    <td class="px-5 py-3 text-right font-mono {{ $item->quantity_received < $item->quantity_ordered ? 'text-amber-400' : 'text-emerald-400' }}">
                        {{ $item->quantity_received }}
                    </td>
                    <td class="px-5 py-3 text-right font-medium text-white font-mono">${{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t border-slate-600 bg-slate-900/30">
                <tr>
                    <td colspan="5" class="px-5 py-2 text-right text-xs text-slate-400">Shipping: ${{ number_format($purchaseOrder->shipping_cost, 2) }}</td>
                    <td class="px-5 py-2 text-right text-xs text-slate-400">Tax: ${{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                    <td class="px-5 py-3 text-right font-bold text-white">${{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Receive form --}}
    @if(in_array($purchaseOrder->status, ['ordered', 'partial']))
    <div id="receive-form" class="hidden bg-slate-800 border border-slate-700 rounded-xl p-6">
        <h2 class="font-semibold text-white mb-4">Receive Items</h2>
        <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="space-y-3">
            @csrf
            @foreach($purchaseOrder->items as $item)
            @if($item->quantity_pending > 0)
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <p class="text-sm text-white">{{ $item->product->name }}</p>
                    <p class="text-xs text-slate-400">Pending: {{ $item->quantity_pending }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-400">Receive:</label>
                    <input type="number" name="quantities[{{ $item->id }}]" value="{{ $item->quantity_pending }}"
                           min="0" max="{{ $item->quantity_pending }}"
                           class="w-20 bg-slate-700 border border-slate-600 text-white rounded px-2 py-1 text-sm">
                </div>
            </div>
            @endif
            @endforeach
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Confirm Receipt & Update Inventory</button>
        </form>
    </div>
    @endif

    @if($purchaseOrder->notes)
    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
        <p class="text-xs text-slate-400 mb-1">Notes</p>
        <p class="text-sm text-slate-300">{{ $purchaseOrder->notes }}</p>
    </div>
    @endif

</div>
@endsection
