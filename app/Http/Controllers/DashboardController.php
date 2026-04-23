<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Player;
use App\Models\PurchaseOrder;
use App\Models\Tournament;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index()
    {
        $stats = Cache::remember('dashboard.stats', 60, function () {
            return [
                'inventory' => $this->inventoryService->getTotalInventoryValue(),
                'active_tournaments' => Tournament::whereIn('status', ['registration', 'active', 'top_cut'])->count(),
                'total_players' => Player::active()->count(),
                'pending_orders' => PurchaseOrder::whereIn('status', ['ordered', 'partial'])->count(),
            ];
        });

        $lowStockItems = InventoryItem::lowStock()
            ->with('product')
            ->orderByRaw('quantity_on_hand - reorder_point ASC')
            ->limit(8)
            ->get();

        $upcomingTournaments = Tournament::upcoming()
            ->whereIn('status', ['registration', 'active'])
            ->withCount('registrations')
            ->limit(5)
            ->get();

        $recentOrders = PurchaseOrder::with('supplier')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'lowStockItems', 'upcomingTournaments', 'recentOrders'));
    }
}
