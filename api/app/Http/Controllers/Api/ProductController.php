<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $products = $this->productRepository->paginate(15);
        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        $product = $this->productRepository->create($request->validated());
        return new ProductResource($product);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product = $this->productRepository->update($product, $request->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productRepository->delete($product);
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
