<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

test('root route renders dashboard component', function () {
    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('todoCount')
        ->has('shoppingCount')
    );
});

test('root route shows null counts for unauthenticated users', function () {
    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->where('todoCount', null)
        ->where('shoppingCount', null)
    );
});

test('root route shows counts for authenticated users', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('todoCount')
        ->has('shoppingCount')
    );
});