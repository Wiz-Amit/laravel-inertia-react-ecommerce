<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests cannot access checkout process', function () {
    $response = $this->post(route('checkout.process'));

    $response->assertRedirect(route('login'));
});

test('checkout show redirects to cart', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('checkout.show'));

    $response->assertRedirect(route('cart.show'));
});

test('users cannot checkout with empty cart', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('checkout.process'));

    $response->assertRedirect(route('cart.show'));
    $response->assertSessionHas('error', 'Your cart is empty.');
});

test('users cannot checkout when cart items exceed stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 5]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 10, // More than available stock
    ]);

    $response = $this->actingAs($user)->post(route('checkout.process'));

    $response->assertRedirect(route('cart.show'));
    $response->assertSessionHas('error');
});

test('checkout success requires session_id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('checkout.success'));

    $response->assertRedirect(route('cart.show'));
    $response->assertSessionHas('error', 'Invalid payment information.');
});

test('checkout success creates order and clears cart', function () {
    // Note: This test requires integration with Stripe API
    // To properly test this, you would need to:
    // 1. Use Stripe test mode with test API keys
    // 2. Create actual checkout sessions
    // 3. Or refactor CheckoutController to use dependency injection for Stripe client

    $this->markTestSkipped('Requires Stripe integration or refactoring to use dependency injection');

    Queue::fake();

    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 29.99,
        'stock_quantity' => 10,
    ]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->get(route('checkout.success', [
        'session_id' => 'cs_test_123',
    ]));

    $response->assertRedirect(route('cart.show'));
    $response->assertSessionHas('success', 'Order placed successfully! Thank you for your purchase.');

    // Verify order was created
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'stripe_payment_intent_id' => 'pi_test_123',
        'status' => 'placed',
    ]);

    // Verify order items were created
    $order = Order::where('user_id', $user->id)->first();
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 29.99,
    ]);

    // Verify stock was decreased
    expect($product->fresh()->stock_quantity)->toBe(8);

    // Verify cart was cleared
    $this->assertDatabaseMissing('cart_items', [
        'cart_id' => $cart->id,
    ]);
});

test('checkout success rejects unpaid sessions', function () {
    // Note: This test requires integration with Stripe API
    // To properly test this, you would need to:
    // 1. Use Stripe test mode with test API keys
    // 2. Create actual checkout sessions
    // 3. Or refactor CheckoutController to use dependency injection for Stripe client

    $this->markTestSkipped('Requires Stripe integration or refactoring to use dependency injection');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('checkout.success', [
        'session_id' => 'cs_test_123',
    ]));

    $response->assertRedirect(route('checkout.show'));
    $response->assertSessionHas('error', 'Payment was not successful.');
});
