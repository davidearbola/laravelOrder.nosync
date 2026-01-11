<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderRepository = new OrderRepository();
        $customers = Customer::all();
        $products = Product::where('stock_quantity', '>', 0)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Run CustomerSeeder and ProductSeeder first.');
            return;
        }

        // Create 10 sample orders
        foreach ($customers->take(10) as $customer) {
            // Random number of products per order (1-3)
            $itemCount = rand(1, 3);
            $items = [];

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products->random();

                // Random quantity (1-5, but not exceeding stock)
                $maxQuantity = min(5, $product->stock_quantity);
                if ($maxQuantity < 1) continue;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => rand(1, $maxQuantity),
                ];
            }

            // Create order if we have items
            if (!empty($items)) {
                try {
                    $order = $orderRepository->createOrder($customer->id, $items);
                    $this->command->info("Created order {$order->order_number} for customer {$customer->name}");
                } catch (\Exception $e) {
                    $this->command->error("Failed to create order for {$customer->name}: {$e->getMessage()}");
                }
            }
        }

        $this->command->info('Orders seeding completed!');
    }
}
