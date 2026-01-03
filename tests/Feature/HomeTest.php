<?php

use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('home page can be rendered', function () {
    Product::factory()->count(15)->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
    );
});

test('home page displays products', function () {
    Product::factory()->count(15)->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('bestsellers', 4) // Default limit
        ->has('newArrivals', 4) // Default limit
    );
});

test('home page displays bestsellers', function () {
    Product::factory()->count(10)->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('bestsellers', 4) // Default limit
    );
});

test('home page displays new arrivals', function () {
    Product::factory()->count(10)->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('newArrivals', 4) // Default limit
    );
});

test('home page handles empty products gracefully', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('bestsellers', 0)
        ->has('newArrivals', 0)
    );
});
