<?php

use App\Models\ShoppingHistoryItem;
use App\Models\ShoppingListItem;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('checkout creates history items', function () {
    $user = User::factory()->create();
    $item = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Milk',
        'quantity' => 2,
        'categories' => ['Dairy'],
        'in_basket' => true,
    ]);

    $response = $this->actingAs($user)->post('/shopping/checkout');

    $response->assertRedirect(route('shopping.index'));
    
    // History item should be created
    $this->assertDatabaseHas('shopping_history_items', [
        'user_id' => $user->id,
        'name' => 'Milk',
        'quantity' => 2,
    ]);
    
    $historyItem = ShoppingHistoryItem::where('user_id', $user->id)
        ->where('name', 'Milk')
        ->first();
    
    expect($historyItem->categories)->toBe(['Dairy']);
    expect($historyItem->purchased_at)->not->toBeNull();
});

test('authenticated user can view shopping history library', function () {
    $user = User::factory()->create();
    ShoppingHistoryItem::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get('/shopping/history');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/History')
        ->has('historyItems')
    );
});

test('user can add item from history to shopping list', function () {
    $user = User::factory()->create();
    $historyItem = ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Milk',
        'quantity' => 2,
        'categories' => ['Dairy'],
    ]);

    $response = $this->actingAs($user)->post('/shopping/history/add', [
        'history_item_id' => $historyItem->id,
    ]);

    $response->assertRedirect(route('shopping.index'));
    
    // Item should be added to shopping list
    $this->assertDatabaseHas('shopping_list_items', [
        'user_id' => $user->id,
        'name' => 'Milk',
        'quantity' => 2,
    ]);
    
    $shoppingItem = ShoppingListItem::where('user_id', $user->id)
        ->where('name', 'Milk')
        ->first();
    
    expect($shoppingItem->categories)->toBe(['Dairy']);
    expect($shoppingItem->in_basket)->toBeFalse();
});

test('user can search history by category', function () {
    $user = User::factory()->create();
    ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Milk',
        'categories' => ['Dairy'],
    ]);
    ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Bread',
        'categories' => ['Bakery'],
    ]);
    ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Cheese',
        'categories' => ['Dairy'],
    ]);

    $response = $this->actingAs($user)->get('/shopping/history?categories[]=Dairy');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/History')
        ->has('historyItems.data', 2) // Should have 2 items with 'Dairy' category
    );
});

test('user can search history by name', function () {
    $user = User::factory()->create();
    ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Milk',
    ]);
    ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Bread',
    ]);
    ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Milk Chocolate',
    ]);

    $response = $this->actingAs($user)->get('/shopping/history?search=Milk');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/History')
        ->has('historyItems.data', 2) // Should have 2 items with 'Milk' in name
    );
});

test('user sees only their own history items', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    ShoppingHistoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'My Item',
    ]);
    ShoppingHistoryItem::factory()->create([
        'user_id' => $otherUser->id,
        'name' => 'Other Item',
    ]);

    $response = $this->actingAs($user)->get('/shopping/history');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/History')
        ->has('historyItems.data', 1)
        ->where('historyItems.data.0.name', 'My Item')
    );
});

test('history library requires authentication', function () {
    $response = $this->get('/shopping/history');

    $response->assertRedirect(route('login'));
});

test('adding from history requires authentication', function () {
    $response = $this->post('/shopping/history/add', [
        'history_item_id' => 1,
    ]);

    $response->assertRedirect(route('login'));
});
