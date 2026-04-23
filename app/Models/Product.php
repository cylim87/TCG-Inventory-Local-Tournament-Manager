<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sku', 'category', 'card_set_id', 'game', 'description',
        'msrp', 'cost_price', 'boxes_per_carton', 'packs_per_box', 'cards_per_pack',
        'barcode', 'image_url', 'is_active',
    ];

    protected $casts = [
        'msrp' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function cardSet()
    {
        return $this->belongsTo(CardSet::class);
    }

    public function inventoryItem()
    {
        return $this->hasOne(InventoryItem::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForGame($query, string $game)
    {
        return $query->where('game', $game);
    }

    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getMarginAttribute(): float
    {
        return $this->msrp - $this->cost_price;
    }

    public function getMarginPercentAttribute(): float
    {
        if ($this->msrp <= 0) return 0;
        return round(($this->getMarginAttribute() / $this->msrp) * 100, 2);
    }

    public function getCartonCostAttribute(): float
    {
        return $this->cost_price * $this->boxes_per_carton;
    }

    public function getCartonMsrpAttribute(): float
    {
        return $this->msrp * $this->boxes_per_carton;
    }

    public function getCartonMarginAttribute(): float
    {
        return $this->getCartonMsrpAttribute() - $this->getCartonCostAttribute();
    }

    public static function categoryLabel(string $category): string
    {
        return match($category) {
            'booster_box' => 'Booster Box',
            'carton' => 'Carton',
            'booster_pack' => 'Booster Pack',
            'single_card' => 'Single Card',
            'elite_trainer_box' => 'Elite Trainer Box',
            'starter_deck' => 'Starter Deck',
            'bundle' => 'Bundle',
            'accessory' => 'Accessory',
            'supply' => 'Supply',
            default => 'Other',
        };
    }
}
