@extends('layouts.app')
@section('title', 'New Purchase Order')

@section('content')
<div class="max-w-4xl mt-2" x-data="poForm()">
    <form method="POST" action="{{ route('purchase-orders.store') }}" class="space-y-6">
        @csrf

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <h2 class="font-semibold text-white">Order Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Supplier *</label>
                    <select name="supplier_id" required class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">— Select Supplier —</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Order Date *</label>
                    <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Expected Delivery</label>
                    <input type="date" name="expected_date" value="{{ old('expected_date') }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Shipping Cost</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="shipping_cost" value="{{ old('shipping_cost', 0) }}" step="0.01" min="0"
                               x-model.number="shipping" @input="recalc()"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Discount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0"
                               x-model.number="discount" @input="recalc()"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Tax</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-sm">$</span>
                        <input type="number" name="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0"
                               x-model.number="tax" @input="recalc()"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-6 pr-3 py-2 text-sm">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Line items --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-white">Order Items</h2>
                <button type="button" @click="addItem()" class="text-xs text-indigo-400 hover:text-indigo-300">+ Add Item</button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 items-start">
                        <div class="col-span-5">
                            <select :name="`items[${index}][product_id]`" x-model="item.product_id"
                                    @change="onProductChange(index)" required
                                    class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-2 py-2 text-sm">
                                <option value="">— Select Product —</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}"
                                        data-cost="{{ $p->cost_price }}"
                                        data-msrp="{{ $p->msrp }}"
                                        data-boxes="{{ $p->boxes_per_carton }}">
                                    {{ $p->name }} ({{ $p->game }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" :name="`items[${index}][quantity_ordered]`"
                                   x-model.number="item.qty" @input="recalc()" min="1" placeholder="Qty"
                                   class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-2 py-2 text-sm">
                        </div>
                        <div class="col-span-2 relative">
                            <span class="absolute left-2 top-2 text-slate-400 text-xs">$</span>
                            <input type="number" :name="`items[${index}][unit_cost]`"
                                   x-model.number="item.cost" @input="recalc()" step="0.01" min="0" placeholder="Unit cost"
                                   class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg pl-5 pr-2 py-2 text-sm">
                        </div>
                        <div class="col-span-2 flex items-center justify-end pt-1.5">
                            <span class="text-sm text-slate-300 font-mono" x-text="'$' + (item.qty * item.cost).toFixed(2)"></span>
                        </div>
                        <div class="col-span-1 flex items-center justify-end pt-1.5">
                            <button type="button" @click="removeItem(index)" class="text-slate-500 hover:text-red-400 text-lg">×</button>
                        </div>
                        {{-- Margin hint --}}
                        <div class="col-span-12 -mt-1 text-xs text-slate-500" x-show="item.product_id && item.cost > 0" x-cloak>
                            <span x-text="marginHint(item)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Totals --}}
            <div class="border-t border-slate-700 pt-4 space-y-1 text-sm">
                <div class="flex justify-between text-slate-400">
                    <span>Subtotal</span><span x-text="'$' + subtotal.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Shipping</span><span x-text="'$' + shipping.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Tax</span><span x-text="'$' + tax.toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Discount</span><span class="text-red-400" x-text="'-$' + discount.toFixed(2)"></span>
                </div>
                <div class="flex justify-between font-bold text-white text-base border-t border-slate-700 pt-2">
                    <span>Total</span><span x-text="'$' + total.toFixed(2)"></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Create Purchase Order</button>
            <a href="{{ route('purchase-orders.index') }}" class="text-slate-400 hover:text-slate-300 text-sm">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function poForm() {
    const productData = {
        @foreach($products as $p)
        '{{ $p->id }}': { cost: {{ $p->cost_price }}, msrp: {{ $p->msrp }}, boxes: {{ $p->boxes_per_carton }} },
        @endforeach
    };

    return {
        items: [{ product_id: '', qty: 1, cost: 0 }],
        shipping: 0, discount: 0, tax: 0, subtotal: 0, total: 0,
        addItem() { this.items.push({ product_id: '', qty: 1, cost: 0 }); },
        removeItem(i) { if (this.items.length > 1) { this.items.splice(i, 1); this.recalc(); } },
        onProductChange(i) {
            const p = productData[this.items[i].product_id];
            if (p) { this.items[i].cost = p.cost; this.recalc(); }
        },
        recalc() {
            this.subtotal = this.items.reduce((sum, i) => sum + (i.qty * i.cost), 0);
            this.total = this.subtotal + this.shipping + this.tax - this.discount;
        },
        marginHint(item) {
            const p = productData[item.product_id];
            if (!p || item.cost <= 0) return '';
            const margin = ((p.msrp - item.cost) / p.msrp * 100).toFixed(1);
            const cartonMargin = ((p.msrp - item.cost) * p.boxes).toFixed(2);
            return `Margin: ${margin}% ($${(p.msrp - item.cost).toFixed(2)}/unit) · Carton profit: $${cartonMargin}`;
        }
    };
}
</script>
@endpush
@endsection
