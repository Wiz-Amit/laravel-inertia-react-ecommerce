<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Display a listing of the user's orders.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 10);

        $orders = $this->orderService->getUserOrders($user, $perPage);

        return Inertia::render('Orders/Index', [
            /** @phpstan-ignore-next-line */
            'orders' => $orders->through(fn ($order) => OrderResource::make($order)),
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, int $id): Response
    {
        $user = $request->user();

        $order = $this->orderService->getUserOrder($user, $id);

        if (!$order) {
            abort(404);
        }

        return Inertia::render('Orders/Show', [
            'order' => OrderResource::make($order),
        ]);
    }
}
