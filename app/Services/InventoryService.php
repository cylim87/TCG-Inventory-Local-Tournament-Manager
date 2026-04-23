<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Receive a purchase order — update stock and record transactions.
     */
    public function receivePurchaseOrder(PurchaseOrder $order, array $receivedQuantities, int $userId): void
    {
        DB::transaction(function () use ($order, $receivedQuantities, $userId) {
            $allReceived = true;
            $anyReceived = false;

            foreach ($order->items as $item) {
                $received = $receivedQuantities[$item->id] ?? 0;
                if ($received <= 0) continue;

                $received = min($received, $item->quantity_ordered - $item->quantity_received);
                if ($received <= 0) continue;

                $anyReceived = true;
                $item->quantity_received += $received;
                $item->save();

                $this->addStock($item->product_id, $received, $item->unit_cost, $userId, 'purchase', $order->id);

                if ($item->quantity_received < $item->quantity_ordered) {
                    $allReceived = false;
                }
            }

            if ($anyReceived) {
                $order->status = $allReceived ? 'received' : 'partial';
                if ($allReceived) {
                    $order->received_date = now()->toDateString();
                }
                $order->save();
            }
        });
    }

    /**
     * Add stock to a product and update the inventory item's weighted-average cost.
     */
    public function addStock(int $productId, int $quantity, float $unitCost, int $userId, string $type = 'purchase', ?int $referenceId = null): void
    {
        $inventoryItem = InventoryItem::firstOrCreate(
            ['product_id' => $productId],
            ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'reorder_point' => 5, 'reorder_quantity' => 10, 'average_cost' => $unitCost]
        );

        // Weighted average cost
        $existingValue = $inventoryItem->quantity_on_hand * $inventoryItem->average_cost;
        $newValue = $quantity * $unitCost;
        $newQty = $inventoryItem->quantity_on_hand + $quantity;
        $inventoryItem->average_cost = $newQty > 0 ? ($existingValue + $newValue) / $newQty : $unitCost;
        $inventoryItem->quantity_on_hand = $newQty;
        $inventoryItem->save();

        InventoryTransaction::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'type' => $type,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'reference_type' => $referenceId ? 'purchase_order' : null,
            'reference_id' => $referenceId,
        ]);
    }

    /**
     * Remove stock from a product (sale, damage, etc.).
     */
    public function removeStock(int $productId, int $quantity, int $userId, string $type = 'sale', string $notes = ''): bool
    {
        $inventoryItem = InventoryItem::where('product_id', $productId)->first();

        if (!$inventoryItem || $inventoryItem->quantity_on_hand < $quantity) {
            return false;
        }

        DB::transaction(function () use ($inventoryItem, $productId, $quantity, $userId, $type, $notes) {
            $inventoryItem->quantity_on_hand -= $quantity;
            $inventoryItem->save();

            InventoryTransaction::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'type' => $type,
                'quantity' => -$quantity,
                'unit_cost' => $inventoryItem->average_cost,
                'notes' => $notes,
            ]);
        });

        return true;
    }

    /**
     * Adjust stock to a specific quantity (inventory count correction).
     */
    public function adjustStock(int $productId, int $newQuantity, int $userId, string $notes = ''): void
    {
        DB::transaction(function () use ($productId, $newQuantity, $userId, $notes) {
            $inventoryItem = InventoryItem::firstOrCreate(
                ['product_id' => $productId],
                ['quantity_on_hand' => 0, 'quantity_reserved' => 0, 'reorder_point' => 5, 'reorder_quantity' => 10, 'average_cost' => 0]
            );

            $difference = $newQuantity - $inventoryItem->quantity_on_hand;
            $inventoryItem->quantity_on_hand = $newQuantity;
            $inventoryItem->last_counted_at = now();
            $inventoryItem->save();

            InventoryTransaction::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'type' => 'count',
                'quantity' => $difference,
                'unit_cost' => $inventoryItem->average_cost,
                'notes' => $notes ?: "Stock count adjustment: {$difference > 0 ? '+' : ''}{$difference}",
            ]);
        });
    }

    /**
     * Get the total retail value of all inventory.
     */
    public function getTotalInventoryValue(): array
    {
        $items = InventoryItem::with('product')->get();

        return [
            'cost_value' => $items->sum(fn($i) => $i->quantity_on_hand * $i->average_cost),
            'retail_value' => $items->sum(fn($i) => $i->quantity_on_hand * $i->product->msrp),
            'total_units' => $items->sum('quantity_on_hand'),
            'total_skus' => $items->count(),
            'low_stock_count' => $items->where('is_low_stock', true)->count(),
            'out_of_stock_count' => $items->where('is_out_of_stock', true)->count(),
        ];
    }
}
