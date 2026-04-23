@extends('layouts.app')
@section('title', 'Purchase Orders')

@section('actions')
    <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
        + New Order
    </a>
@endsection

@section('content')
<div class="space-y-4 mt-2">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            @foreach(['draft' => 'Draft', 'ordered' => 'Ordered', 'partial' => 'Partial', 'received' => 'Received', 'cancelled' => 'Cancelled'] as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="supplier_id" class="bg-slate-800 border border-slate-600 text-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Suppliers</option>
            @foreach($suppliers as $id => $name)
            <option value="{{ $id }}" {{ request('supplier_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
    </form>

    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                    <th class="px-5 py-3 font-medium">PO Number</th>
                    <th class="px-5 py-3 font-medium">Supplier</th>
                    <th class="px-5 py-3 font-medium">Order Date</th>
                    <th class="px-5 py-3 font-medium">Expected</th>
                    <th class="px-5 py-3 font-medium text-right">Total</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-700/30">
                    <td class="px-5 py-3">
                        <a href="{{ route('purchase-orders.show', $order) }}" class="text-indigo-400 hover:text-indigo-300 font-mono text-xs">
                            {{ $order->po_number }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-slate-300">{{ $order->supplier->name }}</td>
                    <td class="px-5 py-3 text-slate-400">{{ $order->order_date->format('M j, Y') }}</td>
                    <td class="px-5 py-3 text-slate-400">{{ $order->expected_date?->format('M j, Y') ?? '—' }}</td>
                    <td class="px-5 py-3 text-right font-medium text-white">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                            @if($order->status === 'received') bg-emerald-900/50 text-emerald-300
                            @elseif($order->status === 'ordered') bg-blue-900/50 text-blue-300
                            @elseif($order->status === 'partial') bg-amber-900/50 text-amber-300
                            @elseif($order->status === 'cancelled') bg-red-900/50 text-red-300
                            @else bg-slate-700 text-slate-400 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No purchase orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-slate-700">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
