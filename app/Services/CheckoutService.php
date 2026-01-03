<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Laravel\Cashier\Cashier;

class CheckoutService
{
    public function __construct(
        private CartService $cartService,
        private ProductService $productService
    ) {}

    /**
     * Validate cart and stock availability for checkout.
     * Throws exception if validation fails.
     *
     * @throws \Exception
     */
    public function validateCartForCheckout(User $user): Cart
    {
        $cart = $this->cartService->getCartWithItems($user);

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Your cart is empty.');
        }

        /** @var CartItem $item */
        foreach ($cart->items as $item) {
            $product = $item->product;

            if (!$product instanceof Product) {
                throw new \Exception('Product not found in cart item.');
            }

            if (!$this->productService->checkStockAvailability($product, $item->quantity)) {
                throw new \Exception("Insufficient stock for {$product->name}.");
            }
        }

        return $cart;
    }

    /**
     * Build Stripe line items from cart.
     *
     * @return array<int, array<string, mixed>>
     */
    public function buildStripeLineItems(Cart $cart): array
    {
        $lineItems = [];

        /** @var CartItem $item */
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (!$product instanceof Product) {
                continue;
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->name,
                        'description' => $product->description ?? '',
                    ],
                    'unit_amount' => (int) ($product->price * 100), // Convert to cents
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Add tax as a separate line item
        $taxAmount = $cart->tax;
        if ($taxAmount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Tax',
                        'description' => 'Sales tax',
                    ],
                    'unit_amount' => (int) ($taxAmount * 100), // Convert to cents
                ],
                'quantity' => 1,
            ];
        }

        return $lineItems;
    }

    /**
     * Create Stripe checkout session.
     *
     * @return string Checkout session URL
     */
    public function createCheckoutSession(User $user, Cart $cart): string
    {
        $lineItems = $this->buildStripeLineItems($cart);
        $stripe = Cashier::stripe();

        $successUrl = url(route('checkout.success', [], false)) . '?session_id={CHECKOUT_SESSION_ID}';

        $checkoutSession = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => url(route('checkout.show', [], false)),
            'metadata' => [
                'user_id' => (string) $user->id,
                'cart_id' => (string) $cart->id,
            ],
        ]);

        return $checkoutSession->url;
    }
}
