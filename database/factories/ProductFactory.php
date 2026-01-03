<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Random kitchenware/eco-friendly product images from Unsplash
        $images = [
            'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1606761568499-6d2451b23c66?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&h=800&fit=crop&q=80',
            'https://images.unsplash.com/photo-1556911220-e15b29be4ba4?w=800&h=800&fit=crop&q=80',
        ];

        return [
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 200),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'description' => fake()->sentence(),
            'image' => fake()->randomElement($images),
        ];
    }
}
