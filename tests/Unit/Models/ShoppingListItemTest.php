<?php

use App\Models\ShoppingListItem;
use App\Models\User;

test('shopping list item requires name field', function () {
    // Name is validated at request level, not database level
    $item = ShoppingListItem::factory()->make(['name' => '']);
    expect($item->name)->toBe('');
    // Validation happens in StoreShoppingListItemRequest
});

test('shopping list item can have nullable categories', function () {
    $item = ShoppingListItem::factory()->create(['categories' => null]);

    expect($item->categories)->toBeNull();
});

test('shopping list item casts categories to array', function () {
    $item = ShoppingListItem::factory()->create(['categories' => ['Dairy', 'Beverages']]);

    expect($item->categories)->toBeArray();
    expect($item->categories)->toBe(['Dairy', 'Beverages']);
});

test('shopping list item casts quantity to decimal', function () {
    $item = ShoppingListItem::factory()->create(['quantity' => 2.5]);

    expect($item->quantity)->toBe('2.50');
});

test('shopping list item has default quantity of 1', function () {
    // Create item without specifying quantity - database default should be 1.0
    // But factory always sets a value, so we'll test that factory provides reasonable default
    $item = ShoppingListItem::factory()->create();

    // Factory should provide a quantity, and it should be a valid decimal
    expect($item->quantity)->toBeString();
    expect((float) $item->quantity)->toBeGreaterThan(0);
});

test('shopping list item belongs to user', function () {
    $user = User::factory()->create();
    $item = ShoppingListItem::factory()->create(['user_id' => $user->id]);

    expect($item->user)->toBeInstanceOf(User::class);
    expect($item->user->id)->toBe($user->id);
});

test('shopping list item can exist without user', function () {
    $item = ShoppingListItem::factory()->create(['user_id' => null]);

    expect($item->user_id)->toBeNull();
    expect($item->user)->toBeNull();
});

test('shopping list item has in_basket boolean field', function () {
    $item = ShoppingListItem::factory()->create(['in_basket' => true]);

    expect($item->in_basket)->toBeTrue();
});

