<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * List all products.
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllProducts(int $perPage = 10)
    {
        return $this->productRepository->listAllProducts($perPage);
    }

    /**
     * Find a product by ID.
     * 
     * @param string $productId
     * @return \App\Models\MstProduk|null
     */
    public function findProductById(string $productId)
    {
        return $this->productRepository->findProductById($productId);
    }

    /**
     * Create a new product.
     * 
     * @param array $data
     * @return \App\Models\MstProduk
     */
    public function createProduct(array $data)
    {
        return $this->productRepository->createProduct($data);
    }

    /**
     * Update an existing product.
     * 
     * @param string $productId
     * @param array $data
     * @return \App\Models\MstProduk|null
     */
    public function updateProduct(string $productId, array $data)
    {
        return $this->productRepository->updateProduct($productId, $data);
    }

    /**
     * Delete a product by ID.
     * 
     * @param string $productId
     * @return bool
     */
    public function deleteProductById(string $productId): bool
    {
        return $this->productRepository->deleteProductById($productId);
    }
}
