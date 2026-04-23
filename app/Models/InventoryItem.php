<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'product_id', 'quantity_on_hand', 'quantity_reserved',
        'reorder_point', 'reorder_quantity', 'location', 'average_cost', 'last_counted_at',
    ];

    protected $casts = [
        'average_cost' => 'decimal:2',
        'last_counted_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getQuantityAvailableAttribute(): int
    {
        return $this->quantity_on_hand - $this->quantity_reserved;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity_on_hand <= $this->reorder_point;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->quantity_on_hand <= 0;
    }

    public function getStockValueAttribute(): float
    {
        return $this->quantity_on_hand * $this->average_cost;
    }

    public function getStockValueAtMsrpAttribute(): float
    {
        return $this->quantity_on_hand * $this->product->msrp;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->is_out_of_stock) return 'out_of_stock';
        if ($this->is_low_stock) return 'low';
        return 'ok';
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_on_hand', '<=', 'reorder_point');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_on_hand', '<=', 0);
    }
}
