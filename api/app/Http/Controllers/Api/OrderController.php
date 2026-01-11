<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [
            'status' => $request->query('status'),
            'search' => $request->query('search'),
        ];

        $orders = $this->orderRepository->paginate(15, $filters);
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderRepository->createOrder(
                $request->customer_id,
                $request->items
            );

            return response()->json([
                'message' => 'Order created successfully',
                'data' => new OrderResource($order)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Order $order): OrderResource
    {
        return new OrderResource($order->load(['customer', 'items.product']));
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        try {
            $order = $this->orderRepository->update($order, $request->validated());
            return response()->json([
                'message' => 'Order updated successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Order $order): JsonResponse
    {
        try {
            $this->orderRepository->delete($order);
            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
