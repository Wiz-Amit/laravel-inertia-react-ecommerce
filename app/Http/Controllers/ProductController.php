<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display the home page.
     */
    public function home(): Response
    {
        $bestsellers = $this->productService->getBestsellers(4);
        $newArrivals = $this->productService->getNewArrivals(4);

        return Inertia::render('Home', [
            'bestsellers' => ProductResource::collection($bestsellers),
            'newArrivals' => ProductResource::collection($newArrivals),
        ]);
    }

    /**
     * Display a listing of products.
     */
    public function index(): Response
    {
        $perPage = request()->get('per_page', 15);
        $search = request()->get('search');
        $products = $this->productService->getAllProducts($perPage, $search);

        return Inertia::render('Products/Index', [
            /** @phpstan-ignore-next-line */
            'products' => $products->through(fn ($product) => ProductResource::make($product)),
            'search' => $search,
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): Response
    {
        $product = $this->productService->getProduct($id);

        if (!$product) {
            abort(404);
        }

        $relatedProducts = $this->productService->getRelatedProducts($id, 4);

        return Inertia::render('Products/Show', [
            'product' => ProductResource::make($product),
            'relatedProducts' => ProductResource::collection($relatedProducts),
        ]);
    }
}
