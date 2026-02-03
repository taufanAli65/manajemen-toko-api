<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * List all products (paginated).
     * Accessible by: all authenticated users
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $products = $this->productService->listAllProducts($perPage);
        
        return ProductResource::collection($products);
    }

    /**
     * Get a single product by ID.
     * Accessible by: all authenticated users
     *
     * @param string $productId
     * @return ProductResource|JsonResponse
     */
    public function show(string $productId)
    {
        $product = $this->productService->findProductById($productId);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        
        return new ProductResource($product);
    }

    /**
     * Create a new product.
     * Accessible by: superadmin, admin
     *
     * @param CreateProductRequest $request
     * @return ProductResource
     */
    public function store(CreateProductRequest $request): ProductResource
    {
        $product = $this->productService->createProduct($request->validated());
        
        return new ProductResource($product);
    }

    /**
     * Update an existing product.
     * Accessible by: superadmin, admin
     *
     * @param UpdateProductRequest $request
     * @param string $productId
     * @return ProductResource|JsonResponse
     */
    public function update(UpdateProductRequest $request, string $productId)
    {
        $product = $this->productService->updateProduct($productId, $request->validated());
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found or update failed'
            ], 404);
        }
        
        return new ProductResource($product);
    }

    /**
     * Soft delete a product.
     * Accessible by: superadmin, admin
     *
     * @param string $productId
     * @return JsonResponse
     */
    public function destroy(string $productId): JsonResponse
    {
        $deleted = $this->productService->deleteProductById($productId);
        
        if (!$deleted) {
            return response()->json([
                'message' => 'Product not found or delete failed'
            ], 404);
        }
        
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
}
