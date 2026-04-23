<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Tournament;
use App\Services\MarginCalculatorService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct(
        private MarginCalculatorService $marginCalculator,
        private InventoryService $inventoryService
    ) {}

    public function margins(Request $request)
    {
        $query = Product::active()
            ->with(['inventoryItem', 'cardSet'])
            ->whereIn('category', ['booster_box', 'carton', 'elite_trainer_box'])
            ->when($request->game, fn($q) => $q->where('game', $request->game));

        $products = $query->orderBy('game')->orderBy('name')->get();

        $analyses = $products->map(fn($p) => $this->marginCalculator->analyze($p));

        $summary = [
            'avg_margin_percent' => $analyses->avg('margin_percent'),
            'total_carton_margin' => $analyses->sum('carton_margin'),
            'best_product' => $analyses->sortByDesc('margin_percent')->first(),
            'worst_product' => $analyses->sortBy('margin_percent')->first(),
        ];

        return view('reports.margins', compact('analyses', 'summary'));
    }

    public function inventory(Request $request)
    {
        $items = InventoryItem::with('product')
            ->when($request->game, fn($q) => $q->whereHas('product', fn($p) => $p->where('game', $request->game)))
            ->get();

        $summary = $this->inventoryService->getTotalInventoryValue();

        $byGame = $items->groupBy(fn($i) => $i->product->game)
            ->map(fn($group) => [
                'cost_value' => $group->sum(fn($i) => $i->quantity_on_hand * $i->average_cost),
                'retail_value' => $group->sum(fn($i) => $i->quantity_on_hand * $i->product->msrp),
                'units' => $group->sum('quantity_on_hand'),
                'skus' => $group->count(),
            ]);

        return view('reports.inventory', compact('items', 'summary', 'byGame'));
    }

    public function tournaments(Request $request)
    {
        $tournaments = Tournament::where('status', 'completed')
            ->withCount('registrations')
            ->when($request->game, fn($q) => $q->where('game', $request->game))
            ->orderByDesc('date')
            ->get();

        $summary = [
            'total_events' => $tournaments->count(),
            'total_players' => $tournaments->sum('registrations_count'),
            'total_revenue' => $tournaments->sum(fn($t) => $t->registrations_count * $t->entry_fee),
            'avg_players' => $tournaments->avg('registrations_count'),
            'by_game' => $tournaments->groupBy('game')->map(fn($g) => [
                'count' => $g->count(),
                'total_players' => $g->sum('registrations_count'),
                'revenue' => $g->sum(fn($t) => $t->registrations_count * $t->entry_fee),
            ]),
        ];

        return view('reports.tournaments', compact('tournaments', 'summary'));
    }
}
