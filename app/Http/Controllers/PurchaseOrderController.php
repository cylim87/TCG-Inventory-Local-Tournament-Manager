<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $orders = PurchaseOrder::with('supplier')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest('order_date')
            ->paginate(20)
            ->withQueryString();

        $suppliers = Supplier::active()->orderBy('name')->pluck('name', 'id');

        return view('purchase-orders.index', compact('orders', 'suppliers'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('game')->orderBy('name')->get();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        DB::transaction(function () use ($request, &$order) {
            $order = PurchaseOrder::create([
                ...$request->validated(),
                'user_id' => auth()->id(),
                'po_number' => PurchaseOrder::generatePoNumber(),
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                if (!isset($item['product_id']) || !$item['quantity_ordered']) continue;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            $order->recalculateTotal();
        });

        return redirect()->route('purchase-orders.show', $order)->with('success', "Purchase order {$order->po_number} created.");
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:0',
        ]);

        $this->inventoryService->receivePurchaseOrder(
            $purchaseOrder,
            $request->quantities,
            auth()->id()
        );

        return back()->with('success', 'Items received and inventory updated.');
    }

    public function markOrdered(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be marked as ordered.');
        }

        $purchaseOrder->update(['status' => 'ordered']);
        return back()->with('success', 'Order marked as placed with supplier.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Cannot cancel a received order.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);
        return back()->with('success', 'Purchase order cancelled.');
    }
}
