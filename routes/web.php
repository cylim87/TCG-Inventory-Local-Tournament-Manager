<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PairingController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication
Auth::routes(['register' => false]);

// Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/adjust/{product}', [InventoryController::class, 'adjust'])->name('adjust');
        Route::get('/transactions/{product}', [InventoryController::class, 'transactions'])->name('transactions');
    });

    // Products
    Route::resource('products', ProductController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['show']);

    // Purchase Orders
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::post('/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('receive');
        Route::post('/{purchaseOrder}/mark-ordered', [PurchaseOrderController::class, 'markOrdered'])->name('mark-ordered');
        Route::post('/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');
    });

    // Players
    Route::resource('players', PlayerController::class);

    // Tournaments
    Route::prefix('tournaments')->name('tournaments.')->group(function () {
        Route::get('/', [TournamentController::class, 'index'])->name('index');
        Route::get('/create', [TournamentController::class, 'create'])->name('create');
        Route::post('/', [TournamentController::class, 'store'])->name('store');
        Route::get('/{tournament}', [TournamentController::class, 'show'])->name('show');
        Route::get('/{tournament}/edit', [TournamentController::class, 'edit'])->name('edit');
        Route::put('/{tournament}', [TournamentController::class, 'update'])->name('update');

        Route::post('/{tournament}/start', [TournamentController::class, 'start'])->name('start');
        Route::post('/{tournament}/complete', [TournamentController::class, 'complete'])->name('complete');
        Route::post('/{tournament}/register', [TournamentController::class, 'register'])->name('register');
        Route::delete('/{tournament}/unregister/{registration}', [TournamentController::class, 'unregister'])->name('unregister');
        Route::post('/{tournament}/drop/{registration}', [TournamentController::class, 'drop'])->name('drop');
        Route::post('/{tournament}/paid/{registration}', [TournamentController::class, 'updatePaid'])->name('update-paid');

        // Rounds
        Route::post('/{tournament}/rounds', [RoundController::class, 'store'])->name('rounds.store');
        Route::get('/{tournament}/rounds/{round}', [RoundController::class, 'show'])->name('rounds.show');
        Route::post('/{tournament}/rounds/{round}/complete', [RoundController::class, 'complete'])->name('rounds.complete');

        // Pairings
        Route::put('/{tournament}/rounds/{round}/pairings/{pairing}', [PairingController::class, 'update'])->name('pairings.update');
        Route::post('/{tournament}/rounds/{round}/pairings/{pairing}/reset', [PairingController::class, 'reset'])->name('pairings.reset');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/margins', [ReportController::class, 'margins'])->name('margins');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/tournaments', [ReportController::class, 'tournaments'])->name('tournaments');
    });
});
