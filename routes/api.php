<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());

    // Quick player search for registration autocomplete
    Route::get('/players/search', function (Request $request) {
        $term = $request->get('q', '');
        return \App\Models\Player::active()
            ->search($term)
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'player_number']);
    });

    // Product search for PO creation
    Route::get('/products/search', function (Request $request) {
        $term = $request->get('q', '');
        return \App\Models\Product::active()
            ->where('name', 'like', "%{$term}%")
            ->with('inventoryItem')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'game', 'category', 'cost_price', 'msrp']);
    });

    // Live standings for a tournament
    Route::get('/tournaments/{tournament}/standings', function (\App\Models\Tournament $tournament) {
        return app(\App\Services\StandingsService::class)->calculateStandings($tournament);
    });

    // Inventory summary
    Route::get('/inventory/summary', function () {
        return app(\App\Services\InventoryService::class)->getTotalInventoryValue();
    });
});
