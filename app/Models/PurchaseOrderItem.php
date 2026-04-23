<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id', 'quantity_ordered', 'quantity_received', 'unit_cost',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getLineTotalAttribute(): float
    {
        return $this->quantity_ordered * $this->unit_cost;
    }

    public function getQuantityPendingAttribute(): int
    {
        return $this->quantity_ordered - $this->quantity_received;
    }

    public function getPerBoxCostAttribute(): float
    {
        return $this->unit_cost;
    }

    public function getBoxMarginAttribute(): float
    {
        return $this->product->msrp - $this->unit_cost;
    }

    public function getBoxMarginPercentAttribute(): float
    {
        if ($this->product->msrp <= 0) return 0;
        return round(($this->getBoxMarginAttribute() / $this->product->msrp) * 100, 2);
    }
}
