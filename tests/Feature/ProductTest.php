<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests can browse products', function () {
    Product::factory()->count(5)->create();

    $response = $this->get(route('products.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Index')
        ->has('products.data', 5)
    );
});

test('guests can view a single product', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 29.99,
        'stock_quantity' => 10,
    ]);

    $response = $this->get(route('products.show', $product->id));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Show')
        ->where('product.id', $product->id)
        ->where('product.name', 'Test Product')
        ->where('product.price', '29.99') // Price is returned as string from database
        ->where('product.stock_quantity', 10)
    );
});

test('product listing shows pagination', function () {
    Product::factory()->count(20)->create();

    $response = $this->get(route('products.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Index')
        ->has('products.data', 15) // default per page
        ->has('products.current_page')
        ->has('products.last_page')
    );
});

test('non-existent product returns 404', function () {
    $response = $this->get(route('products.show', 999));

    $response->assertNotFound();
});

test('product listing supports search by name', function () {
    Product::factory()->create(['name' => 'Blue Enamel Bowl']);
    Product::factory()->create(['name' => 'Red Enamel Plate']);
    Product::factory()->create(['name' => 'Green Ceramic Cup']);

    $response = $this->get(route('products.index', ['search' => 'Enamel']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Index')
        ->has('products.data', 2) // Blue Enamel Bowl and Red Enamel Plate
        ->where('search', 'Enamel')
    );
});

test('product listing supports search by description', function () {
    Product::factory()->create([
        'name' => 'Product A',
        'description' => 'Beautiful kitchenware item',
    ]);
    Product::factory()->create([
        'name' => 'Product B',
        'description' => 'Durable tableware',
    ]);
    Product::factory()->create([
        'name' => 'Product C',
        'description' => 'Elegant serving dish',
    ]);

    $response = $this->get(route('products.index', ['search' => 'kitchenware']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Index')
        ->has('products.data', 1)
        ->where('search', 'kitchenware')
    );
});

test('product show page displays related products', function () {
    $product = Product::factory()->create();
    Product::factory()->count(5)->create();

    $response = $this->get(route('products.show', $product->id));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Show')
        ->has('relatedProducts', 4) // Default limit
    );
});

test('product show page excludes current product from related products', function () {
    $product = Product::factory()->create(['name' => 'Current Product']);
    Product::factory()->count(5)->create();

    $response = $this->get(route('products.show', $product->id));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Show')
        ->has('relatedProducts', 4)
        ->where('relatedProducts.0.id', fn ($id) => $id !== $product->id)
    );
});

test('product listing supports pagination with search', function () {
    Product::factory()->count(25)->create(['name' => 'Test Product']);

    $response = $this->get(route('products.index', [
        'search' => 'Test',
        'per_page' => 10,
    ]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Products/Index')
        ->has('products.data', 10)
        ->where('products.per_page', 10)
        ->where('search', 'Test')
    );
});
