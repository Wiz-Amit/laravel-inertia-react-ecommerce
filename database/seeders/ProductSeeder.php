<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create sample products extracted from Falcon Enamelware
        $products = [
            [
                'name' => 'Bake Set',
                'price' => 128.00,
                'stock_quantity' => 15,
                'description' => 'Complete baking set for all your kitchen needs.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-bundttin-bluewhite-rgb-orig_crop_1200x.jpg?v=1687446223',
            ],
            [
                'name' => 'Tall Tumbler',
                'price' => 16.00,
                'stock_quantity' => 30,
                'description' => 'Classic enamel tumbler for your favorite drinks.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-tall_tumbler-original_white_blue-rgb_2048x_5e956c9f-3e46-4621-a5e2-8697760f2d42_1200x.jpg?v=1756299981',
            ],
            [
                'name' => 'Fruit Bowl',
                'price' => 98.00,
                'stock_quantity' => 12,
                'description' => 'Beautiful enamel fruit bowl for your kitchen.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-fruitbowl-mustardyellow-rgb-pkg_A_1200x.jpg?v=1701014546',
            ],
            [
                'name' => 'Prep Set',
                'price' => 120.00,
                'stock_quantity' => 10,
                'description' => 'Complete prep set for food preparation.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/products/falcon-prep_set-original_white_blue-pkg-rgb_55130599-680e-4309-92c3-d35d58138dd8_1200x.jpg?v=1653565172',
            ],
            [
                'name' => 'Decorative Cake Pan',
                'price' => 47.00,
                'stock_quantity' => 20,
                'description' => 'Decorative enamel cake pan for baking.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-caketin-bluewhite-rgb-orig_crop_1200x.jpg?v=1687445262',
            ],
            [
                'name' => 'Cake Stand',
                'price' => 98.00,
                'stock_quantity' => 15,
                'description' => 'Elegant cake stand for displaying your creations.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-cakestand-pillarbopxred-rgb-pkg_A_1200x.jpg?v=1701014252',
            ],
            [
                'name' => 'Cake Pan',
                'price' => 30.50,
                'stock_quantity' => 25,
                'description' => 'Standard enamel cake pan for everyday baking.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-caketin-bluewhite-rgb-orig_crop_1200x.jpg?v=1687445262',
            ],
            [
                'name' => 'Oval Plate',
                'price' => 35.00,
                'stock_quantity' => 30,
                'description' => 'Classic oval enamel plate for serving.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-ovalplateupright-bluewhite-rgb-orig_crop_1200x.jpg?v=1687436315',
            ],
            [
                'name' => '3 Pint Jug',
                'price' => 55.00,
                'stock_quantity' => 18,
                'description' => 'Large enamel jug for serving drinks.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/products/falcon-large_jug-original_blue_white-pkg-rgb_1200x.jpg?v=1651582900',
            ],
            [
                'name' => 'Dog Bowl',
                'price' => 34.00,
                'stock_quantity' => 40,
                'description' => 'Durable enamel bowl for your pet.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-dogbowl-white-rgb-orig_crop_1200x.jpg?v=1687433441',
            ],
            [
                'name' => 'Pie Set',
                'price' => 101.00,
                'stock_quantity' => 12,
                'description' => 'Complete pie dish set for baking.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/products/falcon-pie_set-original_white_blue-pkg-rgb_6a08d9cb-5a99-4200-bc67-83c60bd75c2d_1200x.jpg?v=1651611971',
            ],
            [
                'name' => 'Soap Dish',
                'price' => 22.50,
                'stock_quantity' => 35,
                'description' => 'Elegant enamel soap dish for your bathroom.',
                'image' => 'https://us.falconenamelware.com/cdn/shop/files/falcon-soapdish-bluewhite-rgb-orig_crop_1200x.jpg?v=1687434921',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
