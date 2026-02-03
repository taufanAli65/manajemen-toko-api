<?php

namespace Tests\Feature\Api\V1;

use App\Models\MstProduk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    /** @test */
    public function test_list_products_success()
    {
        $user = $this->createUser();
        $this->createProduct(['name' => 'Product 1']);
        $this->createProduct(['name' => 'Product 2']);

        $response = $this->actingAsUser($user)
            ->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function test_list_products_search()
    {
        $user = $this->createUser();
        $this->createProduct(['name' => 'Laptop ASUS']);
        $this->createProduct(['name' => 'Mouse Logitech']);

        $response = $this->actingAsUser($user)
            ->getJson('/api/v1/products?search=Laptop');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Laptop ASUS']);
    }

    /** @test */
    public function test_create_product_as_admin()
    {
        $admin = $this->createUser(['role' => 'admin']);
        
        $productData = [
            'name' => 'New Product',
            'merk' => 'Brand X',
            'harga' => 100000
        ];

        $response = $this->actingAsUser($admin)
            ->postJson('/api/v1/products', $productData);

        $response->assertStatus(201)
            ->assertJson(['name' => 'New Product']);

        $this->assertDatabaseHas('mst_produk', ['name' => 'New Product']);
    }

    /** @test */
    public function test_create_product_forbidden()
    {
        $kasir = $this->createUser(['role' => 'kasir']);
        
        $productData = [
            'name' => 'New Product',
            'merk' => 'Brand X',
            'harga' => 100000
        ];

        $response = $this->actingAsUser($kasir)
            ->postJson('/api/v1/products', $productData);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_get_product_detail()
    {
        $user = $this->createUser();
        $product = $this->createProduct();

        $response = $this->actingAsUser($user)
            ->getJson("/api/v1/products/{$product->product_id}");

        $response->assertStatus(200)
            ->assertJson(['product_id' => $product->product_id]);
    }

    /** @test */
    public function test_get_nonexistent_product()
    {
        $user = $this->createUser();
        $fakeId = \Illuminate\Support\Str::uuid();

        $response = $this->actingAsUser($user)
            ->getJson("/api/v1/products/{$fakeId}");

        $response->assertStatus(404);
    }

    /** @test */
    public function test_update_product_success()
    {
        $admin = $this->createUser(['role' => 'admin']);
        $product = $this->createProduct(['name' => 'Old Name', 'harga' => 5000]);

        $response = $this->actingAsUser($admin)
            ->putJson("/api/v1/products/{$product->product_id}", [
                'name' => 'New Name',
                'harga' => 7000,
                'merk' => 'Brand Y'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'New Name',
                'harga' => 7000
            ]);
    }

    /** @test */
    public function test_delete_product_success()
    {
        $admin = $this->createUser(['role' => 'admin']);
        $product = $this->createProduct();

        $response = $this->actingAsUser($admin)
            ->deleteJson("/api/v1/products/{$product->product_id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('mst_produk', ['product_id' => $product->product_id]);
    }

    protected function createProduct(array $attributes = [])
    {
        return MstProduk::create(array_merge([
            'name' => fake()->word(),
            'merk' => fake()->company(),
            'harga' => fake()->numberBetween(1000, 100000),
            'created_by' => 'system'
        ], $attributes));
    }
}
