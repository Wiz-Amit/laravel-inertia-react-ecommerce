<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CheckoutService $checkoutService,
        private OrderService $orderService
    ) {}

    /**
     * Show the checkout page (redirects to cart).
     */
    public function show(): RedirectResponse
    {
        return redirect()->route('cart.show');
    }

    /**
     * Process the checkout and create payment intent.
     */
    public function process(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $cart = $this->checkoutService->validateCartForCheckout($user);
            $checkoutUrl = $this->checkoutService->createCheckoutSession($user, $cart);

            return redirect($checkoutUrl);
        } catch (\Exception $e) {
            return redirect()->route('cart.show')->with('error', $e->getMessage());
        }
    }

    /**
     * Handle successful payment.
     */
    public function success(Request $request): Response|RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('cart.show')->with('error', 'Invalid payment information.');
        }

        try {
            $stripe = Cashier::stripe();
            $checkoutSession = $stripe->checkout->sessions->retrieve($sessionId);

            if ($checkoutSession->payment_status !== 'paid') {
                return redirect()->route('cart.show')->with('error', 'Payment was not successful.');
            }

            $user = $request->user();
            $cart = $this->cartService->getCartWithItems($user);

            if (!$cart) {
                return redirect()->route('cart.show')->with('error', 'Cart not found.');
            }

            // Create order (includes order items creation and stock decrease)
            $order = $this->orderService->createOrderFromCart(
                $user,
                $cart,
                $checkoutSession->payment_intent
            );

            // Clear cart
            $this->cartService->clearCart($user);

            return Inertia::render('Checkout/Success', [
                'order' => OrderResource::make($order),
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cart.show')->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }
}
