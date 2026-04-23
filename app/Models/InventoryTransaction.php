<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'type', 'quantity', 'unit_cost',
        'reference_type', 'reference_id', 'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'purchase' => 'green',
            'sale' => 'blue',
            'adjustment' => 'yellow',
            'return' => 'indigo',
            'damage' => 'red',
            'transfer' => 'purple',
            'count' => 'gray',
            default => 'gray',
        };
    }
}
