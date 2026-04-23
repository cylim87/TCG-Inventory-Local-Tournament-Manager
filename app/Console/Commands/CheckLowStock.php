<?php

namespace App\Console\Commands;

use App\Models\InventoryItem;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock';
    protected $description = 'Report products that have fallen below their reorder point';

    public function handle(): int
    {
        $items = InventoryItem::with('product')
            ->lowStock()
            ->orderBy('quantity_on_hand')
            ->get();

        if ($items->isEmpty()) {
            $this->info('All stock levels are healthy.');
            return 0;
        }

        $this->warn("⚠  {$items->count()} product(s) need reordering:");

        $rows = $items->map(fn($i) => [
            $i->product->name,
            $i->product->game,
            $i->quantity_on_hand,
            $i->reorder_point,
            $i->is_out_of_stock ? 'OUT OF STOCK' : 'Low',
        ])->toArray();

        $this->table(['Product', 'Game', 'On Hand', 'Reorder At', 'Status'], $rows);

        return 0;
    }
}
