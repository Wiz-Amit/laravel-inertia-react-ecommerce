<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Get or create cart for user.
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Get cart with items for user.
     * Loads relationships and forces evaluation of accessors (subtotal, tax, total).
     */
    public function getCartWithItems(User $user): ?Cart
    {
        $cart = Cart::with(['items.product'])->where('user_id', $user->id)->first();

        if ($cart) {
            // Force evaluation of cart accessors (subtotal, tax, total) before serialization
            $cart->toArray();
        }

        return $cart;
    }

    /**
     * Add item to cart.
     */
    public function addItem(User $user, int $productId, int $quantity): CartItem
    {
        $product = Product::findOrFail($productId);

        if (!$this->productService->checkStockAvailability($product, $quantity)) {
            throw new \Exception('Insufficient stock available.');
        }

        return DB::transaction(function () use ($user, $productId, $quantity, $product) {
            $cart = $this->getOrCreateCart($user);

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
            } else {
                $cartItem = new CartItem([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                ]);
                $newQuantity = $quantity;
            }

            if (!$this->productService->checkStockAvailability($product, $newQuantity)) {
                throw new \Exception('Insufficient stock available.');
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();

            return $cartItem;
        });
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(User $user, int $cartItemId, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw new \Exception('Quantity must be greater than 0.');
        }

        return DB::transaction(function () use ($user, $cartItemId, $quantity) {
            $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($cartItemId);

            $product = $cartItem->product;
            if (!$product instanceof Product) {
                throw new ModelNotFoundException('Product not found for cart item.');
            }

            if (!$this->productService->checkStockAvailability($product, $quantity)) {
                throw new \Exception('Insufficient stock available.');
            }

            $cartItem->quantity = $quantity;
            $cartItem->save();

            return $cartItem;
        });
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(User $user, int $cartItemId): void
    {
        DB::transaction(function () use ($user, $cartItemId) {
            $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($cartItemId);

            $cartItem->delete();
        });
    }

    /**
     * Clear all items from cart.
     */
    public function clearCart(User $user): void
    {
        $cart = $this->getCartWithItems($user);

        if ($cart) {
            $cart->items()->delete();
        }
    }
}
