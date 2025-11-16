<?php

use App\Models\ShoppingListItem;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('user can view shopping list items index page', function () {
    $response = $this->get('/shopping');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/Index')
        ->has('items')
    );
});

test('user can create a shopping list item', function () {
    $data = [
        'name' => 'Milk',
        'quantity' => 2,
        'categories' => ['Dairy', 'Beverages'],
    ];

    $response = $this->post('/shopping', $data);

    $response->assertRedirect(route('shopping.index'));
    $this->assertDatabaseHas('shopping_list_items', [
        'name' => 'Milk',
        'quantity' => 2,
    ]);

    $item = ShoppingListItem::where('name', 'Milk')->first();
    expect($item->categories)->toBe(['Dairy', 'Beverages']);
    expect($item->quantity)->toBe('2.00');
});

test('user can create shopping list item without authentication', function () {
    $data = [
        'name' => 'Bread',
        'quantity' => 1,
    ];

    $response = $this->post('/shopping', $data);

    $response->assertRedirect(route('shopping.index'));
    $this->assertDatabaseHas('shopping_list_items', [
        'name' => 'Bread',
        'user_id' => null,
    ]);
});

test('user can view edit page for shopping list item', function () {
    $item = ShoppingListItem::factory()->create();

    $response = $this->get("/shopping/{$item->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/Edit')
        ->has('item')
        ->where('item.id', $item->id)
    );
});

test('user can update a shopping list item', function () {
    $item = ShoppingListItem::factory()->create([
        'name' => 'Original name',
        'quantity' => 1,
    ]);

    $data = [
        'name' => 'Updated name',
        'quantity' => 3,
        'categories' => ['Produce'],
    ];

    $response = $this->put("/shopping/{$item->id}", $data);

    $response->assertRedirect(route('shopping.index'));
    $this->assertDatabaseHas('shopping_list_items', [
        'id' => $item->id,
        'name' => 'Updated name',
        'quantity' => 3,
    ]);

    $item->refresh();
    expect($item->categories)->toBe(['Produce']);
});

test('user can delete a shopping list item', function () {
    $item = ShoppingListItem::factory()->create();

    $response = $this->delete("/shopping/{$item->id}");

    $response->assertRedirect(route('shopping.index'));
    $this->assertDatabaseMissing('shopping_list_items', [
        'id' => $item->id,
    ]);
});

test('user can filter shopping items by category', function () {
    ShoppingListItem::factory()->create(['categories' => ['Dairy', 'Beverages']]);
    ShoppingListItem::factory()->create(['categories' => ['Produce']]);
    ShoppingListItem::factory()->create(['categories' => ['Dairy']]);

    $response = $this->get('/shopping?categories[]=Dairy');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/Index')
        ->has('items', 2) // Should have 2 items with 'Dairy' category
    );
});

test('authenticated user sees only their shopping items', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    ShoppingListItem::factory()->create(['user_id' => $user->id, 'name' => 'My item']);
    ShoppingListItem::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other item']);
    ShoppingListItem::factory()->create(['user_id' => null, 'name' => 'Public item']);

    $response = $this->actingAs($user)->get('/shopping');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/Index')
        ->has('items', 1) // Should only see user's items
        ->where('items.0.name', 'My item')
    );
});

test('unauthenticated user sees items without user_id', function () {
    ShoppingListItem::factory()->create(['user_id' => null, 'name' => 'Public item']);
    ShoppingListItem::factory()->create(['user_id' => User::factory()->create()->id, 'name' => 'Private item']);

    $response = $this->get('/shopping');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Shopping/Index')
        ->has('items', 1) // Should only see public items
        ->where('items.0.name', 'Public item')
    );
});

test('user can mark shopping item as in basket', function () {
    $user = User::factory()->create();
    $item = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'in_basket' => false,
    ]);

    $response = $this->actingAs($user)->put("/shopping/{$item->id}", [
        'name' => $item->name,
        'quantity' => $item->quantity,
        'in_basket' => true,
    ]);

    $response->assertRedirect(route('shopping.index'));
    $this->assertDatabaseHas('shopping_list_items', [
        'id' => $item->id,
        'in_basket' => true,
    ]);
});

test('user can unmark shopping item from basket', function () {
    $user = User::factory()->create();
    $item = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'in_basket' => true,
    ]);

    $response = $this->actingAs($user)->put("/shopping/{$item->id}", [
        'name' => $item->name,
        'quantity' => $item->quantity,
        'in_basket' => false,
    ]);

    $response->assertRedirect(route('shopping.index'));
    $this->assertDatabaseHas('shopping_list_items', [
        'id' => $item->id,
        'in_basket' => false,
    ]);
});

test('user can checkout items in basket', function () {
    $user = User::factory()->create();
    $item1 = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Milk',
        'quantity' => 2,
        'categories' => ['Dairy'],
        'in_basket' => true,
    ]);
    $item2 = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Bread',
        'quantity' => 1,
        'categories' => ['Bakery'],
        'in_basket' => true,
    ]);
    $item3 = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Eggs',
        'quantity' => 1,
        'in_basket' => false, // Not in basket
    ]);

    $response = $this->actingAs($user)->post('/shopping/checkout');

    $response->assertRedirect(route('shopping.index'));
    
    // Items in basket should be deleted from shopping list
    $this->assertDatabaseMissing('shopping_list_items', ['id' => $item1->id]);
    $this->assertDatabaseMissing('shopping_list_items', ['id' => $item2->id]);
    
    // Item not in basket should remain
    $this->assertDatabaseHas('shopping_list_items', ['id' => $item3->id]);
    
    // Items should be added to shopping history
    $this->assertDatabaseHas('shopping_history_items', [
        'user_id' => $user->id,
        'name' => 'Milk',
        'quantity' => 2,
    ]);
    $this->assertDatabaseHas('shopping_history_items', [
        'user_id' => $user->id,
        'name' => 'Bread',
        'quantity' => 1,
    ]);
});

test('checkout only processes items in basket', function () {
    $user = User::factory()->create();
    $item1 = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Item 1',
        'in_basket' => true,
    ]);
    $item2 = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Item 2',
        'in_basket' => false,
    ]);

    $response = $this->actingAs($user)->post('/shopping/checkout');

    $response->assertRedirect(route('shopping.index'));
    
    // Only item1 should be deleted
    $this->assertDatabaseMissing('shopping_list_items', ['id' => $item1->id]);
    $this->assertDatabaseHas('shopping_list_items', ['id' => $item2->id]);
    
    // Only item1 should be in history
    $this->assertDatabaseHas('shopping_history_items', [
        'user_id' => $user->id,
        'name' => 'Item 1',
    ]);
    $this->assertDatabaseMissing('shopping_history_items', [
        'user_id' => $user->id,
        'name' => 'Item 2',
    ]);
});

test('checkout requires authentication', function () {
    $response = $this->post('/shopping/checkout');

    $response->assertRedirect(route('login'));
});
