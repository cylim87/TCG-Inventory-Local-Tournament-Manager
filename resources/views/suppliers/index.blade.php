@extends('layouts.app')
@section('title', 'Suppliers')

@section('actions')
    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium">
        + Add Supplier
    </a>
@endsection

@section('content')
<div class="mt-2">
    <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                    <th class="px-5 py-3 font-medium">Supplier</th>
                    <th class="px-5 py-3 font-medium">Contact</th>
                    <th class="px-5 py-3 font-medium">Phone / Email</th>
                    <th class="px-5 py-3 font-medium text-right">Orders</th>
                    <th class="px-5 py-3 font-medium text-right">Terms</th>
                    <th class="px-5 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($suppliers as $supplier)
                <tr class="{{ !$supplier->is_active ? 'opacity-50' : '' }} hover:bg-slate-700/30">
                    <td class="px-5 py-3">
                        <p class="font-medium text-white">{{ $supplier->name }}</p>
                        @if($supplier->account_number)
                        <p class="text-xs text-slate-500 font-mono">{{ $supplier->account_number }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-slate-400 text-sm">{{ $supplier->contact_name ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @if($supplier->email)<p class="text-slate-400 text-xs">{{ $supplier->email }}</p>@endif
                        @if($supplier->phone)<p class="text-slate-500 text-xs">{{ $supplier->phone }}</p>@endif
                    </td>
                    <td class="px-5 py-3 text-right text-slate-300">{{ $supplier->purchase_orders_count }}</td>
                    <td class="px-5 py-3 text-right text-slate-400 text-xs">Net {{ $supplier->payment_terms_days }}d</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-xs text-indigo-400 hover:text-indigo-300">Edit</a>
                            <a href="{{ route('purchase-orders.create') }}" class="text-xs text-slate-400 hover:text-white">+ Order</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No suppliers yet. Add your first distributor.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-slate-700">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
