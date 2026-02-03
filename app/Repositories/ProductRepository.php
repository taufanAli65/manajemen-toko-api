<?php

namespace App\Repositories;

use App\Models\MstProduk;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * List all products.
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllProducts(int $perPage = 10)
    {
        return MstProduk::where('is_deleted', false)->paginate($perPage);
    }

    /**
     * Find a product by ID.
     * 
     * @param string $productId
     * @return \App\Models\MstProduk|null
     */
    public function findProductById(string $productId)
    {
        return MstProduk::where('product_id', $productId)
            ->where('is_deleted', false)
            ->first();
    }

    /**
     * Create a new product.
     * 
     * @param array $data
     * @return \App\Models\MstProduk
     */
    public function createProduct(array $data)
    {
        return MstProduk::create([
            'name' => $data['name'],
            'merk' => $data['merk'],
            'harga' => $data['harga'],
        ]);
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
        $product = MstProduk::find($productId);
        if (!$product) {
            return null;
        }
        
        if (isset($data['name'])) {
            $product->name = $data['name'];
        }
        if (isset($data['merk'])) {
            $product->merk = $data['merk'];
        }
        if (isset($data['harga'])) {
            $product->harga = $data['harga'];
        }
        
        $product->save();
        return $product;
    }

    /**
     * Delete a product by ID.
     * 
     * @param string $productId
     * @return bool
     */
    public function deleteProductById(string $productId): bool
    {
        $product = MstProduk::find($productId);
        if (!$product) {
            return false;
        }
        
        $product->is_deleted = true;
        $product->save();
        return true;
    }
}
