<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    /**
     * Get all orders with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['customer' => function($q) {
            $q->withTrashed();
        }, 'items.product']);

        // Apply status filter if provided
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search by order number
        if (!empty($filters['search'])) {
            $query->where('order_number', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get all orders.
     */
    public function all(): Collection
    {
        return Order::with(['customer' => function($q) {
            $q->withTrashed();
        }, 'items.product'])->latest()->get();
    }

    /**
     * Find order by ID.
     */
    public function find(int $id): ?Order
    {
        return Order::with(['customer' => function($q) {
            $q->withTrashed();
        }, 'items.product'])->find($id);
    }

    /**
     * Find order by ID or fail.
     */
    public function findOrFail(int $id): Order
    {
        return Order::with(['customer' => function($q) {
            $q->withTrashed();
        }, 'items.product'])->findOrFail($id);
    }

    /**
     * Create a new order with items and stock management.
     *
     * @param int $customerId
     * @param array $items Array of items with product_id and quantity
     * @return Order
     * @throws \Exception
     */
    public function createOrder(int $customerId, array $items): Order
    {
        return DB::transaction(function () use ($customerId, $items) {
            // 1. Validate stock availability for all products
            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception(
                        "Stock insufficiente per {$product->name}. Disponibili: {$product->stock_quantity}, Richiesti: {$item['quantity']}"
                    );
                }
            }

            // 2. Create order with auto-generated order number
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customerId,
                'date' => now(),
                'status' => OrderStatus::IN_ATTESA,
                'total_amount' => 0, // Will be calculated below
            ]);

            // 3. Create order items and decrease stock
            $totalAmount = 0;
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Create order item
                $orderItem = $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                // Decrease stock
                $product->decrement('stock_quantity', $item['quantity']);

                $totalAmount += $orderItem->subtotal;
            }

            // 4. Update total amount
            $order->update(['total_amount' => $totalAmount]);

            return $order->fresh(['customer', 'items.product']);
        });
    }

    /**
     * Update order.
     */
    public function update(Order $order, array $data): Order
    {
        if (!$order->canBeModified()) {
            throw new \Exception('L\'ordine non può essere modificato');
        }

        $order->update($data);
        return $order->fresh(['customer', 'items.product']);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Order $order, OrderStatus $newStatus): Order
    {
        if (!$order->canTransitionTo($newStatus)) {
            throw new \Exception(
                "Transizione non valida da {$order->status->value} a {$newStatus->value}"
            );
        }

        $order->update(['status' => $newStatus]);
        return $order->fresh(['customer', 'items.product']);
    }

    /**
     * Cancel order and restore stock.
     */
    public function cancelOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            if (!$order->canBeModified()) {
                throw new \Exception('L\'ordine non può essere modificato');
            }

            // Restore stock for all items
            foreach ($order->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            // Update status to cancelled
            $order->update(['status' => OrderStatus::ANNULLATO]);

            return $order->fresh(['customer', 'items.product']);
        });
    }

    /**
     * Delete order (only if modifiable).
     */
    public function delete(Order $order): bool
    {
        if (!$order->canBeModified()) {
            throw new \Exception('L\'ordine non può essere eliminato');
        }

        // Restore stock before deletion
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        return $order->delete();
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber(): string
    {
        $year = now()->year;

        $lastOrder = Order::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        $nextNumber = $lastOrder
            ? ((int) substr($lastOrder->order_number, -4)) + 1
            : 1;

        return sprintf('ORD-%d-%04d', $year, $nextNumber);
    }
}
