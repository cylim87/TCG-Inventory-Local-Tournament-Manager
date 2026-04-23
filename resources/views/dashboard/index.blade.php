@extends('layouts.app')
@section('title', 'Dashboard')

@section('actions')
    <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Event
    </a>
@endsection

@section('content')
<div class="space-y-6 mt-2">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Inventory (Cost)</p>
            <p class="text-2xl font-bold text-white">${{ number_format($stats['inventory']['cost_value'], 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Retail: ${{ number_format($stats['inventory']['retail_value'], 0) }}</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">SKUs in Stock</p>
            <p class="text-2xl font-bold text-white">{{ number_format($stats['inventory']['total_skus']) }}</p>
            <p class="text-xs {{ $stats['inventory']['low_stock_count'] > 0 ? 'text-amber-400' : 'text-slate-500' }} mt-1">
                {{ $stats['inventory']['low_stock_count'] }} low stock
            </p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Active Events</p>
            <p class="text-2xl font-bold text-white">{{ $stats['active_tournaments'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $stats['pending_orders'] }} pending orders</p>
        </div>
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Registered Players</p>
            <p class="text-2xl font-bold text-white">{{ number_format($stats['total_players']) }}</p>
            <p class="text-xs text-slate-500 mt-1">All-time registrations</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Upcoming Tournaments --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-700">
                <h2 class="font-semibold text-white">Upcoming Events</h2>
                <a href="{{ route('tournaments.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300">View all →</a>
            </div>
            <div class="divide-y divide-slate-700">
                @forelse($upcomingTournaments as $t)
                <a href="{{ route('tournaments.show', $t) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-slate-700/50 transition-colors">
                    <div class="w-10 h-10 bg-slate-700 rounded-lg flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-slate-300">{{ strtoupper(substr($t->game, 0, 3)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $t->name }}</p>
                        <p class="text-xs text-slate-400">{{ $t->date->format('M j, Y') }} · {{ $t->start_time }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                            {{ $t->status === 'registration' ? 'bg-blue-900/50 text-blue-300' : 'bg-green-900/50 text-green-300' }}">
                            {{ $t->registrations_count }} players
                        </span>
                    </div>
                </a>
                @empty
                <div class="px-5 py-8 text-center text-slate-500 text-sm">No upcoming events.</div>
                @endforelse
            </div>
        </div>

        {{-- Low Stock Alerts --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-700">
                <h2 class="font-semibold text-white">Low Stock Alerts</h2>
                <a href="{{ route('inventory.index', ['status' => 'low']) }}" class="text-xs text-amber-400 hover:text-amber-300">View all →</a>
            </div>
            <div class="divide-y divide-slate-700">
                @forelse($lowStockItems as $item)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-2 h-2 rounded-full shrink-0 {{ $item->is_out_of_stock ? 'bg-red-500' : 'bg-amber-500' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white truncate">{{ $item->product->name }}</p>
                        <p class="text-xs text-slate-400">{{ $item->product->game }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-medium {{ $item->is_out_of_stock ? 'text-red-400' : 'text-amber-400' }}">
                            {{ $item->quantity_on_hand }}
                        </span>
                        <span class="text-xs text-slate-500"> / {{ $item->reorder_point }}</span>
                    </div>
                    <a href="{{ route('purchase-orders.create') }}" class="text-xs text-indigo-400 hover:text-indigo-300 shrink-0">Order</a>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-emerald-500 text-sm">All stock levels healthy ✓</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Recent Purchase Orders --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-700">
            <h2 class="font-semibold text-white">Recent Purchase Orders</h2>
            <a href="{{ route('purchase-orders.create') }}" class="text-xs text-indigo-400 hover:text-indigo-300">+ New Order</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="px-5 py-3 font-medium">PO #</th>
                        <th class="px-5 py-3 font-medium">Supplier</th>
                        <th class="px-5 py-3 font-medium">Date</th>
                        <th class="px-5 py-3 font-medium">Total</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-slate-700/50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="{{ route('purchase-orders.show', $order) }}" class="text-indigo-400 hover:text-indigo-300 font-mono text-xs">
                                {{ $order->po_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-slate-300">{{ $order->supplier->name }}</td>
                        <td class="px-5 py-3 text-slate-400">{{ $order->order_date->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-white font-medium">${{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                @if($order->status === 'received') bg-emerald-900/50 text-emerald-300
                                @elseif($order->status === 'ordered') bg-blue-900/50 text-blue-300
                                @elseif($order->status === 'partial') bg-amber-900/50 text-amber-300
                                @elseif($order->status === 'cancelled') bg-red-900/50 text-red-300
                                @else bg-slate-700 text-slate-400
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No purchase orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
