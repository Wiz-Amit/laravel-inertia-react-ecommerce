<?php

namespace App\Services;

use App\Jobs\LowStockNotificationJob;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Get all products with pagination.
     */
    public function getAllProducts(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get a single product by ID.
     */
    public function getProduct(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Get related products (excluding current product).
     */
    public function getRelatedProducts(int $productId, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('id', '!=', $productId)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get bestseller products.
     */
    public function getBestsellers(int $limit = 4): Collection
    {
        return Product::orderBy('id')
            ->take($limit)
            ->get();
    }

    /**
     * Get new arrival products.
     */
    public function getNewArrivals(int $limit = 4): Collection
    {
        return Product::orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Check if product has enough stock.
     */
    public function checkStockAvailability(Product $product, int $quantity): bool
    {
        return $product->isInStock($quantity);
    }

    /**
     * Decrease product stock and trigger low stock notification if needed.
     */
    public function decreaseStock(Product $product, int $quantity): void
    {
        DB::transaction(function () use ($product, $quantity) {
            $product->decrement('stock_quantity', $quantity);

            $threshold = config('ecommerce.low_stock_threshold', 10);
            $freshProduct = $product->fresh();

            if ($freshProduct->stock_quantity <= $threshold) {
                LowStockNotificationJob::dispatch($product->id, $freshProduct->stock_quantity);
            }
        });
    }
}
