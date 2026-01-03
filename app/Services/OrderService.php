<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Get paginated orders for a user.
     */
    public function getUserOrders(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('user_id', $user->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get a specific order for a user.
     */
    public function getUserOrder(User $user, int $orderId): ?Order
    {
        return Order::where('user_id', $user->id)
            ->with('items.product')
            ->find($orderId);
    }

    /**
     * Create an order from cart and process stock decrease.
     */
    public function createOrderFromCart(
        User $user,
        Cart $cart,
        string $stripePaymentIntentId
    ): Order {
        return DB::transaction(function () use ($user, $cart, $stripePaymentIntentId) {
            $order = Order::create([
                'user_id' => $user->id,
                'stripe_payment_intent_id' => $stripePaymentIntentId,
                'status' => OrderStatus::PLACED,
                'subtotal' => $cart->subtotal,
                'tax' => $cart->tax,
                'total' => $cart->total,
            ]);

            /** @var CartItem $cartItem */
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                if (!$product instanceof \App\Models\Product) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price,
                ]);

                // Decrease stock
                $this->productService->decreaseStock($product, $cartItem->quantity);
            }

            return $order->load('items.product');
        });
    }

    /**
     * Get daily sales data for a specific date from completed orders.
     */
    public function getDailySales(Carbon $date): Collection
    {
        return OrderItem::whereHas('order', function ($query) use ($date) {
            $query->whereDate('created_at', $date->toDateString())
                ->where('status', OrderStatus::PLACED->value);
        })
            ->with('product')
            ->get();
    }

    /**
     * Aggregate daily sales data for reporting.
     */
    public function aggregateDailySales(Carbon $date): array
    {
        $orderItems = $this->getDailySales($date);

        $totalProductsSold = $orderItems->sum('quantity');
        $totalRevenue = $orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $topProducts = $orderItems->groupBy('product_id')
            ->map(function ($items, $productId) {
                $product = $items->first()->product;
                $quantity = $items->sum('quantity');
                $revenue = $items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('quantity')
            ->take(10)
            ->values();

        return [
            'date' => $date->toDateString(),
            'total_products_sold' => $totalProductsSold,
            'total_revenue' => $totalRevenue,
            'top_products' => $topProducts,
        ];
    }
}
