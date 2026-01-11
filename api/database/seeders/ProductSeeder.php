<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific products
        $products = [
            [
                'code' => 'PROD-001',
                'name' => 'Laptop HP',
                'description' => 'Laptop HP 15.6" Intel Core i5',
                'price' => 599.99,
                'stock_quantity' => 15,
            ],
            [
                'code' => 'PROD-002',
                'name' => 'Mouse Wireless',
                'description' => 'Mouse wireless Logitech',
                'price' => 29.99,
                'stock_quantity' => 50,
            ],
            [
                'code' => 'PROD-003',
                'name' => 'Tastiera Meccanica',
                'description' => 'Tastiera meccanica RGB',
                'price' => 89.99,
                'stock_quantity' => 30,
            ],
            [
                'code' => 'PROD-004',
                'name' => 'Monitor 24"',
                'description' => 'Monitor Full HD 24 pollici',
                'price' => 199.99,
                'stock_quantity' => 0, // Out of stock for testing
            ],
            [
                'code' => 'PROD-005',
                'name' => 'Webcam HD',
                'description' => 'Webcam 1080p con microfono',
                'price' => 49.99,
                'stock_quantity' => 8, // Low stock for testing
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create additional random products
        Product::factory(15)->create();
    }
}
