<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoItemRequest;
use App\Http\Requests\UpdateTodoItemRequest;
use App\Models\TodoItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TodoItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = TodoItem::query();

        // Filter by user if authenticated, otherwise show only public todos
        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $query->whereNull('user_id');
        }

        // Filter by tags if provided
        if ($request->has('tags') && is_array($request->tags)) {
            foreach ($request->tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        // Sort by due_date or created_at
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'due_date') {
            $query->orderBy('due_date', $sortOrder === 'asc' ? 'asc' : 'desc')
                ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $todos = $query->get();

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'filters' => [
                'tags' => $request->tags ?? [],
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'canCreate' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Todos/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoItemRequest $request)
    {
        $data = $request->validated();

        // Set user_id if authenticated
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
            $data['synced_at'] = now(); // Mark as synced for authenticated users
        } else {
            $data['user_id'] = null;
        }

        TodoItem::create($data);

        return redirect()->route('todos.index')
            ->with('success', 'Todo item created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TodoItem $todo): Response
    {
        return Inertia::render('Todos/Edit', [
            'todo' => $todo,
            'canUpdate' => true,
            'canDelete' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoItemRequest $request, TodoItem $todo)
    {
        $data = $request->validated();

        // Update synced_at for authenticated users
        if ($request->user()) {
            $data['synced_at'] = now();
        }

        $todo->update($data);

        return redirect()->route('todos.index')
            ->with('success', 'Todo item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoItem $todo)
    {
        $todo->delete();

        return redirect()->route('todos.index')
            ->with('success', 'Todo item deleted successfully.');
    }
}
