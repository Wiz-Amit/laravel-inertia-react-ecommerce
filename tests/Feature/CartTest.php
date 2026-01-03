<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests cannot access cart', function () {
    $response = $this->get(route('cart.show'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view their empty cart', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('cart.show'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Cart/Index')
    );
});

test('authenticated users can add products to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock_quantity' => 10,
    ]);

    $response = $this->actingAs($user)->post(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('carts', [
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('users cannot add more items than available stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock_quantity' => 5,
    ]);

    $response = $this->actingAs($user)->post(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 10, // More than available
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('users cannot add out of stock products', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock_quantity' => 0,
    ]);

    $response = $this->actingAs($user)->post(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('users can update cart item quantity', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock_quantity' => 20,
    ]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->put(route('cart.items.update', $cartItem->id), [
        'quantity' => 5,
    ]);

    $response->assertRedirect(route('cart.show'));

    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 5,
    ]);
});

test('users cannot update quantity to exceed stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'stock_quantity' => 5,
    ]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->put(route('cart.items.update', $cartItem->id), [
        'quantity' => 10, // More than available
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('users can remove items from cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
    ]);

    $response = $this->actingAs($user)->delete(route('cart.items.destroy', $cartItem->id));

    $response->assertRedirect(route('cart.show'));

    $this->assertDatabaseMissing('cart_items', [
        'id' => $cartItem->id,
    ]);
});

test('users can clear their entire cart', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    CartItem::factory()->count(3)->create(['cart_id' => $cart->id]);

    $response = $this->actingAs($user)->delete(route('cart.clear'));

    $response->assertRedirect(route('cart.show'));

    $this->assertDatabaseMissing('cart_items', [
        'cart_id' => $cart->id,
    ]);
});

test('users can only access their own cart', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user2->id]);
    $cartItem = CartItem::factory()->create(['cart_id' => $cart->id]);

    $response = $this->actingAs($user1)->put(route('cart.items.update', $cartItem->id), [
        'quantity' => 5,
    ]);

    $response->assertNotFound();
});

test('adding same product twice increases quantity', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 20]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->post(route('cart.items.store'), [
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 5, // 2 + 3
    ]);
});
