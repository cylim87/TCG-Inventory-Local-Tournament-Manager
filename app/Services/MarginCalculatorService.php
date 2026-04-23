<?php

namespace App\Services;

use App\Models\Product;

class MarginCalculatorService
{
    /**
     * Full margin analysis for a product at its configured cost and MSRP.
     */
    public function analyze(Product $product, ?float $actualCostPerUnit = null, ?float $actualSellPrice = null): array
    {
        $cost = $actualCostPerUnit ?? $product->cost_price;
        $sell = $actualSellPrice ?? $product->msrp;
        $boxes = $product->boxes_per_carton;

        return [
            'product_name' => $product->name,
            'category' => $product->category,
            'game' => $product->game,

            // Per-unit (box) figures
            'cost_per_unit' => $cost,
            'msrp_per_unit' => $product->msrp,
            'sell_price_per_unit' => $sell,
            'margin_per_unit' => $sell - $cost,
            'margin_percent' => $sell > 0 ? round((($sell - $cost) / $sell) * 100, 2) : 0,
            'markup_percent' => $cost > 0 ? round((($sell - $cost) / $cost) * 100, 2) : 0,

            // Carton figures
            'boxes_per_carton' => $boxes,
            'carton_cost' => $cost * $boxes,
            'carton_msrp' => $product->msrp * $boxes,
            'carton_sell_value' => $sell * $boxes,
            'carton_margin' => ($sell - $cost) * $boxes,
            'carton_margin_percent' => $sell > 0 ? round((($sell - $cost) / $sell) * 100, 2) : 0,

            // Break-even analysis
            'break_even_price' => $cost,
            'break_even_at_discount_5' => round($sell * 0.95, 2),
            'break_even_at_discount_10' => round($sell * 0.90, 2),
            'break_even_at_discount_15' => round($sell * 0.85, 2),

            // Pack-level breakdown (if applicable)
            'packs_per_box' => $product->packs_per_box,
            'cost_per_pack' => $product->packs_per_box ? round($cost / $product->packs_per_box, 4) : null,
            'sell_per_pack' => $product->packs_per_box ? round($sell / $product->packs_per_box, 4) : null,
        ];
    }

    /**
     * Calculate margins across a whole carton purchase.
     */
    public function analyzeCarton(
        float $cartonCost,
        float $msrpPerBox,
        int $boxesPerCarton,
        float $shippingCost = 0,
        float $sellPricePerBox = 0
    ): array {
        $totalCost = $cartonCost + $shippingCost;
        $costPerBox = $totalCost / $boxesPerCarton;
        $sell = $sellPricePerBox ?: $msrpPerBox;

        return [
            'carton_cost' => $cartonCost,
            'shipping_cost' => $shippingCost,
            'total_landed_cost' => $totalCost,
            'boxes_per_carton' => $boxesPerCarton,
            'cost_per_box' => round($costPerBox, 2),
            'msrp_per_box' => $msrpPerBox,
            'sell_price_per_box' => $sell,
            'margin_per_box' => round($sell - $costPerBox, 2),
            'margin_percent' => $sell > 0 ? round((($sell - $costPerBox) / $sell) * 100, 2) : 0,
            'total_revenue' => round($sell * $boxesPerCarton, 2),
            'total_margin' => round(($sell - $costPerBox) * $boxesPerCarton, 2),
            'roi_percent' => $totalCost > 0 ? round((($sell * $boxesPerCarton - $totalCost) / $totalCost) * 100, 2) : 0,
            'break_even_price' => round($costPerBox, 2),
            'boxes_to_break_even' => $sell > $costPerBox ? ceil($totalCost / $sell) : $boxesPerCarton,
        ];
    }

    /**
     * Compare selling at different price points.
     */
    public function priceScenarios(float $costPerUnit, float $msrp, int $units): array
    {
        $pricePoints = [
            'msrp' => $msrp,
            '5_off' => $msrp * 0.95,
            '10_off' => $msrp * 0.90,
            '15_off' => $msrp * 0.85,
            '20_off' => $msrp * 0.80,
        ];

        return collect($pricePoints)->map(function ($price, $label) use ($costPerUnit, $units) {
            return [
                'label' => $label,
                'price' => round($price, 2),
                'margin_per_unit' => round($price - $costPerUnit, 2),
                'margin_percent' => $price > 0 ? round((($price - $costPerUnit) / $price) * 100, 2) : 0,
                'total_revenue' => round($price * $units, 2),
                'total_margin' => round(($price - $costPerUnit) * $units, 2),
                'profitable' => $price > $costPerUnit,
            ];
        })->values()->all();
    }
}
