<?php

namespace App\Http\Controllers;

use App\Models\ShoppingListItem;
use App\Models\TodoItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): Response
    {
        $todoCount = null;
        $shoppingCount = null;

        if ($request->user()) {
            $todoCount = TodoItem::where('user_id', $request->user()->id)->count();
            $shoppingCount = ShoppingListItem::where('user_id', $request->user()->id)
                ->where('in_basket', false)
                ->count();
        }

        return Inertia::render('Dashboard', [
            'todoCount' => $todoCount,
            'shoppingCount' => $shoppingCount,
        ]);
    }
}

