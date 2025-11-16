<?php

namespace App\Http\Controllers;

use App\Models\ShoppingHistoryItem;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShoppingHistoryController extends Controller
{
    /**
     * Display the shopping history library.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $query = ShoppingHistoryItem::where('user_id', $user->id);

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Filter by categories
        if ($request->has('categories') && is_array($request->categories)) {
            foreach ($request->categories as $category) {
                $query->whereJsonContains('categories', $category);
            }
        }

        // Sort by purchased_at (most recent first) or name
        $sortBy = $request->get('sort_by', 'purchased_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'name') {
            $query->orderBy('name', $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('purchased_at', $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $historyItems = $query->paginate($perPage)->withQueryString();

        // Get all unique categories for filtering
        $allCategories = ShoppingHistoryItem::where('user_id', $user->id)
            ->whereNotNull('categories')
            ->get()
            ->pluck('categories')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return Inertia::render('Shopping/History', [
            'historyItems' => $historyItems,
            'filters' => [
                'search' => $request->search ?? '',
                'categories' => $request->categories ?? [],
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'allCategories' => $allCategories,
        ]);
    }

    /**
     * Add an item from history to the shopping list.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'history_item_id' => 'required|exists:shopping_history_items,id',
        ]);

        $historyItem = ShoppingHistoryItem::where('user_id', $user->id)
            ->findOrFail($validated['history_item_id']);

        // Check if item already exists in shopping list
        $existingItem = ShoppingListItem::where('user_id', $user->id)
            ->where('name', $historyItem->name)
            ->first();

        if ($existingItem) {
            // Update quantity if item exists
            $existingItem->update([
                'quantity' => $existingItem->quantity + $historyItem->quantity,
            ]);
        } else {
            // Create new shopping list item
            ShoppingListItem::create([
                'user_id' => $user->id,
                'name' => $historyItem->name,
                'quantity' => $historyItem->quantity,
                'categories' => $historyItem->categories,
                'in_basket' => false,
                'synced_at' => now(),
            ]);
        }

        return redirect()->route('shopping.index')
            ->with('success', 'Item added to shopping list.');
    }
}
