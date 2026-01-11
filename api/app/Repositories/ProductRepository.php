<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * Get all products with pagination.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Product::latest()->paginate($perPage);
    }

    /**
     * Get all products.
     */
    public function all(): Collection
    {
        return Product::latest()->get();
    }

    /**
     * Get only products with stock available.
     */
    public function availableProducts(): Collection
    {
        return Product::where('stock_quantity', '>', 0)->get();
    }

    /**
     * Find product by ID.
     */
    public function find(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Find product by ID or fail.
     */
    public function findOrFail(int $id): Product
    {
        return Product::findOrFail($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update product.
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    /**
     * Delete product.
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Check if product has sufficient stock.
     */
    public function hasStock(Product $product, int $quantity): bool
    {
        return $product->stock_quantity >= $quantity;
    }

    /**
     * Decrease product stock.
     */
    public function decreaseStock(Product $product, int $quantity): void
    {
        $product->decrement('stock_quantity', $quantity);
    }

    /**
     * Increase product stock.
     */
    public function increaseStock(Product $product, int $quantity): void
    {
        $product->increment('stock_quantity', $quantity);
    }
}
