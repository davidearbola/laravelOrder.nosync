<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepository $customerRepository
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [
            'show_deleted' => $request->query('show_deleted'),
            'search' => $request->query('search'),
        ];

        $customers = $this->customerRepository->paginate(15, $filters);
        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request): CustomerResource
    {
        $customer = $this->customerRepository->create($request->validated());
        return new CustomerResource($customer);
    }

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer->load('orders'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer = $this->customerRepository->update($customer, $request->validated());
        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        try {
            $this->customerRepository->delete($customer);
            return response()->json(['message' => 'Cliente eliminato con successo']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function restore(int $id): JsonResponse
    {
        try {
            $customer = $this->customerRepository->restore($id);
            return response()->json([
                'message' => 'Cliente ripristinato con successo',
                'data' => new CustomerResource($customer)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
