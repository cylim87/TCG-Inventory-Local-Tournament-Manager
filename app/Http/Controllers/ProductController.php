<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\CardSet;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Services\MarginCalculatorService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private MarginCalculatorService $marginCalculator) {}

    public function index(Request $request)
    {
        $query = Product::with(['cardSet', 'inventoryItem'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->game, fn($q) => $q->where('game', $request->game))
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->active !== null, fn($q) => $q->where('is_active', $request->active));

        $products = $query->orderBy('game')->orderBy('name')->paginate(20)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'games' => $this->gameOptions(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['cardSet', 'inventoryItem', 'purchaseOrderItems.purchaseOrder']);
        $analysis = $this->marginCalculator->analyze($product);
        $scenarios = $this->marginCalculator->priceScenarios($product->cost_price, $product->msrp, $product->boxes_per_carton);

        return view('products.show', compact('product', 'analysis', 'scenarios'));
    }

    public function create()
    {
        $cardSets = CardSet::active()->orderBy('game')->orderBy('name')->get();
        return view('products.create', [
            'cardSets' => $cardSets,
            'games' => $this->gameOptions(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        InventoryItem::create([
            'product_id' => $product->id,
            'quantity_on_hand' => 0,
            'reorder_point' => $request->reorder_point ?? 5,
            'reorder_quantity' => $request->reorder_quantity ?? 10,
        ]);

        return redirect()->route('products.show', $product)->with('success', "Product \"{$product->name}\" created successfully.");
    }

    public function edit(Product $product)
    {
        $cardSets = CardSet::active()->orderBy('game')->orderBy('name')->get();
        return view('products.edit', [
            'product' => $product,
            'cardSets' => $cardSets,
            'games' => $this->gameOptions(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return redirect()->route('products.show', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => false]);
        return redirect()->route('products.index')->with('success', 'Product deactivated.');
    }

    private function gameOptions(): array
    {
        return [
            'pokemon' => 'Pokémon',
            'mtg' => 'Magic: The Gathering',
            'yugioh' => 'Yu-Gi-Oh!',
            'one_piece' => 'One Piece',
            'lorcana' => 'Disney Lorcana',
            'fab' => 'Flesh and Blood',
            'digimon' => 'Digimon',
            'union_arena' => 'Union Arena',
            'other' => 'Other',
        ];
    }

    private function categoryOptions(): array
    {
        return [
            'booster_box' => 'Booster Box',
            'carton' => 'Carton',
            'booster_pack' => 'Booster Pack',
            'single_card' => 'Single Card',
            'elite_trainer_box' => 'Elite Trainer Box',
            'starter_deck' => 'Starter Deck',
            'bundle' => 'Bundle',
            'accessory' => 'Accessory',
            'supply' => 'Supply',
            'other' => 'Other',
        ];
    }
}
