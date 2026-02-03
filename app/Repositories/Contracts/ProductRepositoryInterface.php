<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    /**
     * List all products.
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllProducts(int $perPage = 10);

    /**
     * Find a product by ID.
     * 
     * @param string $productId
     * @return \App\Models\MstProduk|null
     */
    public function findProductById(string $productId);

    /**
     * Create a new product.
     * 
     * @param array $data
     * @return \App\Models\MstProduk
     */
    public function createProduct(array $data);

    /**
     * Update an existing product.
     * 
     * @param string $productId
     * @param array $data
     * @return \App\Models\MstProduk|null
     */
    public function updateProduct(string $productId, array $data);

    /**
     * Delete a product by ID.
     * 
     * @param string $productId
     * @return bool
     */
    public function deleteProductById(string $productId): bool;
}
