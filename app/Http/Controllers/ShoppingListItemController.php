<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShoppingListItemRequest;
use App\Http\Requests\UpdateShoppingListItemRequest;
use App\Models\ShoppingHistoryItem;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShoppingListItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = ShoppingListItem::query();

        // Filter by user if authenticated, otherwise show only public items
        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $query->whereNull('user_id');
        }

        // Filter by categories if provided
        if ($request->has('categories') && is_array($request->categories)) {
            foreach ($request->categories as $category) {
                $query->whereJsonContains('categories', $category);
            }
        }

        // Sort by name or created_at
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'name') {
            $query->orderBy('name', $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $items = $query->get();

        // Count items in basket
        $basketCount = $items->where('in_basket', true)->count();

        return Inertia::render('Shopping/Index', [
            'items' => $items,
            'filters' => [
                'categories' => $request->categories ?? [],
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'basketCount' => $basketCount,
            'canCreate' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Shopping/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShoppingListItemRequest $request)
    {
        $data = $request->validated();

        // Set user_id if authenticated
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
            $data['synced_at'] = now(); // Mark as synced for authenticated users
        } else {
            $data['user_id'] = null;
        }

        // Set default quantity if not provided
        if (! isset($data['quantity'])) {
            $data['quantity'] = 1.0;
        }

        ShoppingListItem::create($data);

        return redirect()->route('shopping.index')
            ->with('success', 'Shopping list item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShoppingListItem $shopping): Response
    {
        return Inertia::render('Shopping/Edit', [
            'item' => $shopping,
            'canUpdate' => true,
            'canDelete' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShoppingListItemRequest $request, ShoppingListItem $shopping)
    {
        $data = $request->validated();

        // Update synced_at for authenticated users
        if ($request->user()) {
            $data['synced_at'] = now();
        }

        $shopping->update($data);

        return redirect()->route('shopping.index')
            ->with('success', 'Shopping list item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShoppingListItem $shopping)
    {
        $shopping->delete();

        return redirect()->route('shopping.index')
            ->with('success', 'Shopping list item deleted successfully.');
    }

    /**
     * Checkout items in basket - move them to history and remove from list.
     */
    public function checkout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Get all items in basket for the authenticated user
        $basketItems = ShoppingListItem::where('user_id', $user->id)
            ->where('in_basket', true)
            ->get();

        if ($basketItems->isEmpty()) {
            return redirect()->route('shopping.index')
                ->with('info', 'No items in basket to checkout.');
        }

        // Move items to shopping history
        foreach ($basketItems as $item) {
            ShoppingHistoryItem::create([
                'user_id' => $user->id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'categories' => $item->categories,
                'purchased_at' => now(),
            ]);
        }

        // Delete items from shopping list
        $basketItems->each->delete();

        return redirect()->route('shopping.index')
            ->with('success', 'Checkout completed. '.$basketItems->count().' item(s) moved to history.');
    }
}
