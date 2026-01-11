<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository
{
    /**
     * Get all customers with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Customer::with('orders');

        // Filter by deleted status
        if (isset($filters['show_deleted']) && $filters['show_deleted'] === 'only') {
            $query->onlyTrashed();
        } elseif (isset($filters['show_deleted']) && $filters['show_deleted'] === 'with') {
            $query->withTrashed();
        }

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get all customers.
     */
    public function all(): Collection
    {
        return Customer::with('orders')->latest()->get();
    }

    /**
     * Find customer by ID.
     */
    public function find(int $id): ?Customer
    {
        return Customer::with('orders')->find($id);
    }

    /**
     * Find customer by ID or fail.
     */
    public function findOrFail(int $id): Customer
    {
        return Customer::with('orders')->findOrFail($id);
    }

    /**
     * Create a new customer.
     */
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    /**
     * Update customer.
     */
    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh(['orders']);
    }

    /**
     * Soft delete customer.
     * Validates that customer has no active orders (in_attesa, in_lavorazione).
     */
    public function delete(Customer $customer): bool
    {
        // Check for active orders
        $activeOrders = $customer->orders()
            ->whereIn('status', ['in_attesa', 'in_lavorazione'])
            ->count();

        if ($activeOrders > 0) {
            throw new \Exception("Non è possibile eliminare il cliente. Ha {$activeOrders} ordini attivi (in attesa o in lavorazione).");
        }

        return $customer->delete();
    }

    /**
     * Restore soft deleted customer.
     */
    public function restore(int $id): Customer
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        return $customer->fresh(['orders']);
    }

    /**
     * Get customer's orders.
     */
    public function getOrders(Customer $customer, int $perPage = 15): LengthAwarePaginator
    {
        return $customer->orders()
            ->with('items.product')
            ->latest()
            ->paginate($perPage);
    }
}
