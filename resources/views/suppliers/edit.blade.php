@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="max-w-xl mt-2">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Edit: {{ $supplier->name }}</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Company Name *</label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Contact Name</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $supplier->contact_name) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $supplier->account_number) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Credit Limit ($)</label>
                    <input type="number" name="credit_limit" value="{{ old('credit_limit', $supplier->credit_limit) }}" min="0" step="100"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Payment Terms (days)</label>
                    <input type="number" name="payment_terms_days" value="{{ old('payment_terms_days', $supplier->payment_terms_days) }}" min="0"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Active</label>
                    <select name="is_active" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="1" {{ $supplier->is_active ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$supplier->is_active ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs text-slate-400 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Save Changes</button>
            <a href="{{ route('suppliers.index') }}" class="text-slate-400 hover:text-slate-300 text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection
