<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('daily sales report command can be executed', function () {
    Mail::fake();

    $yesterday = Carbon::yesterday();
    $user = User::factory()->create();

    // Create products
    $product1 = Product::factory()->create(['price' => 10.00]);
    $product2 = Product::factory()->create(['price' => 20.00]);

    // Create orders from yesterday with PLACED status
    $order1 = Order::create([
        'user_id' => $user->id,
        'stripe_payment_intent_id' => 'pi_test_1',
        'status' => OrderStatus::PLACED,
        'subtotal' => 20.00,
        'tax' => 0.00,
        'total' => 20.00,
    ]);
    $order1->created_at = $yesterday;
    $order1->save();

    OrderItem::create([
        'order_id' => $order1->id,
        'product_id' => $product1->id,
        'quantity' => 2,
        'price' => 10.00,
    ]);

    $order2 = Order::create([
        'user_id' => $user->id,
        'stripe_payment_intent_id' => 'pi_test_2',
        'status' => OrderStatus::PLACED,
        'subtotal' => 20.00,
        'tax' => 0.00,
        'total' => 20.00,
    ]);
    $order2->created_at = $yesterday;
    $order2->save();

    OrderItem::create([
        'order_id' => $order2->id,
        'product_id' => $product2->id,
        'quantity' => 1,
        'price' => 20.00,
    ]);

    // Create order from today (should not be included)
    $order3 = Order::create([
        'user_id' => $user->id,
        'stripe_payment_intent_id' => 'pi_test_3',
        'status' => OrderStatus::PLACED,
        'subtotal' => 10.00,
        'tax' => 0.00,
        'total' => 10.00,
    ]);
    $order3->created_at = Carbon::today();
    $order3->save();

    OrderItem::create([
        'order_id' => $order3->id,
        'product_id' => $product1->id,
        'quantity' => 1,
        'price' => 10.00,
    ]);

    $exitCode = Artisan::call('sales:daily-report', [
        '--date' => $yesterday->toDateString(),
    ]);

    expect($exitCode)->toBe(0);

    Mail::assertSent(\App\Mail\DailySalesReportMail::class);
});

test('daily sales report aggregates sales data correctly', function () {
    $yesterday = Carbon::yesterday();
    $orderService = app(OrderService::class);
    $user = User::factory()->create();

    $product1 = Product::factory()->create(['price' => 10.00]);
    $product2 = Product::factory()->create(['price' => 20.00]);

    // Create orders with order items
    $order1 = Order::create([
        'user_id' => $user->id,
        'stripe_payment_intent_id' => 'pi_test_1',
        'status' => OrderStatus::PLACED,
        'subtotal' => 20.00,
        'tax' => 0.00,
        'total' => 20.00,
    ]);
    $order1->created_at = $yesterday;
    $order1->save();

    OrderItem::create([
        'order_id' => $order1->id,
        'product_id' => $product1->id,
        'quantity' => 2,
        'price' => 10.00,
    ]);

    $order2 = Order::create([
        'user_id' => $user->id,
        'stripe_payment_intent_id' => 'pi_test_2',
        'status' => OrderStatus::PLACED,
        'subtotal' => 20.00,
        'tax' => 0.00,
        'total' => 20.00,
    ]);
    $order2->created_at = $yesterday;
    $order2->save();

    OrderItem::create([
        'order_id' => $order2->id,
        'product_id' => $product2->id,
        'quantity' => 1,
        'price' => 20.00,
    ]);

    $salesData = $orderService->aggregateDailySales($yesterday);

    expect($salesData['total_products_sold'])->toBe(3); // 2 + 1
    expect($salesData['total_revenue'])->toBe(40.00); // (2 * 10) + (1 * 20)
    expect($salesData['date'])->toBe($yesterday->toDateString());
});

test('daily sales report handles empty sales gracefully', function () {
    Mail::fake();

    $yesterday = Carbon::yesterday();

    $exitCode = Artisan::call('sales:daily-report', [
        '--date' => $yesterday->toDateString(),
    ]);

    expect($exitCode)->toBe(0);

    Mail::assertSent(\App\Mail\DailySalesReportMail::class);
});

test('daily sales report uses yesterday as default date', function () {
    Mail::fake();

    $exitCode = Artisan::call('sales:daily-report');

    expect($exitCode)->toBe(0);

    Mail::assertSent(\App\Mail\DailySalesReportMail::class);
});
