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
            'orders' => OrderResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ],
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
