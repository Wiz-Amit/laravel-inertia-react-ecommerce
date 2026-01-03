<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * Display the user's cart with checkout functionality.
     */
    public function show(Request $request): Response
    {
        $cart = $this->cartService->getCartWithItems($request->user());

        $stripeKey = config('services.stripe.key');

        return Inertia::render('Cart/Index', [
            'cart' => $cart ? CartResource::make($cart) : null,
            'stripeKey' => $stripeKey,
        ]);
    }

    /**
     * Add item to cart.
     */
    public function addItem(AddCartItemRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->cartService->addItem(
                $request->user(),
                $validated['product_id'],
                $validated['quantity']
            );

            return redirect()->back()->with('success', 'Item added to cart.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(UpdateCartItemRequest $request, int $id): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->cartService->updateItem(
                $request->user(),
                $id,
                $validated['quantity']
            );

            return redirect()->route('cart.show')->with('success', 'Cart updated.');
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Exception $e) {
            return redirect()->route('cart.show')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(Request $request, int $id): RedirectResponse
    {
        try {
            $this->cartService->removeItem($request->user(), $id);

            return redirect()->route('cart.show')->with('success', 'Item removed from cart.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Clear all items from cart.
     */
    public function clear(Request $request): RedirectResponse
    {
        $this->cartService->clearCart($request->user());

        return redirect()->route('cart.show')->with('success', 'Cart cleared.');
    }
}
