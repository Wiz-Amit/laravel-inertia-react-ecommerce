<?php

use App\Jobs\LowStockNotificationJob;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Queue;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('low stock notification job is dispatched when stock drops below threshold', function () {
    Queue::fake();

    $product = Product::factory()->create([
        'stock_quantity' => 15, // Above threshold
    ]);

    $productService = app(ProductService::class);

    // Decrease stock to below threshold (default is 10)
    $productService->decreaseStock($product, 6); // 15 - 6 = 9, which is below 10

    Queue::assertPushed(LowStockNotificationJob::class, function ($job) use ($product) {
        return $job->productId === $product->id && $job->stockQuantity === 9;
    });
});

test('low stock notification job is not dispatched when stock is above threshold', function () {
    Queue::fake();

    $product = Product::factory()->create([
        'stock_quantity' => 15,
    ]);

    $productService = app(ProductService::class);

    // Decrease stock but keep it above threshold
    $productService->decreaseStock($product, 3); // 15 - 3 = 12, which is above 10

    Queue::assertNothingPushed();
});

test('low stock notification job is dispatched exactly at threshold', function () {
    Queue::fake();

    $product = Product::factory()->create([
        'stock_quantity' => 10, // Exactly at threshold
    ]);

    $productService = app(ProductService::class);

    // Decrease stock to exactly at threshold
    $productService->decreaseStock($product, 1); // 10 - 1 = 9, which is below 10

    Queue::assertPushed(LowStockNotificationJob::class);
});

test('stock is correctly decreased when low stock notification is triggered', function () {
    Queue::fake();

    $product = Product::factory()->create([
        'stock_quantity' => 15,
    ]);

    $productService = app(ProductService::class);
    $productService->decreaseStock($product, 6);

    expect($product->fresh()->stock_quantity)->toBe(9);
});
