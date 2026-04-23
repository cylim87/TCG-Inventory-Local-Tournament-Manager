<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index(Request $request)
    {
        $query = InventoryItem::with('product')
            ->when($request->search, fn($q) => $q->whereHas('product', fn($p) => $p->where('name', 'like', "%{$request->search}%")))
            ->when($request->game, fn($q) => $q->whereHas('product', fn($p) => $p->where('game', $request->game)))
            ->when($request->status === 'low', fn($q) => $q->lowStock())
            ->when($request->status === 'out', fn($q) => $q->outOfStock());

        $items = $query->orderByRaw('quantity_on_hand ASC')->paginate(25)->withQueryString();

        $summary = $this->inventoryService->getTotalInventoryValue();

        return view('inventory.index', compact('items', 'summary'));
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'new_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->inventoryService->adjustStock(
            $product->id,
            $request->new_quantity,
            auth()->id(),
            $request->notes
        );

        return back()->with('success', "Stock for \"{$product->name}\" adjusted to {$request->new_quantity}.");
    }

    public function transactions(Product $product)
    {
        $transactions = InventoryTransaction::where('product_id', $product->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('inventory.transactions', compact('product', 'transactions'));
    }
}
