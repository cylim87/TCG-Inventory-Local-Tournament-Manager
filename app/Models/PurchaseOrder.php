<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number', 'supplier_id', 'user_id', 'status', 'order_date',
        'expected_date', 'received_date', 'shipping_cost', 'discount_amount',
        'tax_amount', 'total_amount', 'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($item) => $item->quantity_ordered * $item->unit_cost);
    }

    public function recalculateTotal(): void
    {
        $subtotal = $this->items()->sum(\DB::raw('quantity_ordered * unit_cost'));
        $this->total_amount = $subtotal + $this->shipping_cost + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    public static function generatePoNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $last = static::whereYear('created_at', $year)->whereMonth('created_at', $month)->max('id') ?? 0;
        return sprintf('PO-%s%s-%04d', $year, $month, $last + 1);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'ordered' => 'blue',
            'partial' => 'yellow',
            'received' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }
}
