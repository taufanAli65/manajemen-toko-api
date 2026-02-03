<?php

namespace Tests\Feature\Api\V1;

use App\Models\MapUserToko;
use App\Models\MstProduk;
use App\Models\TrnTransaksiToko;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    /** @test */
    public function test_create_transaction_success()
    {
        $kasir = $this->createUser(['role' => 'kasir']);
        $toko = $this->createToko();
        
        MapUserToko::create([
            'user_id' => $kasir->user_id,
            'toko_id' => $toko->toko_id
        ]);


        $product1 = $this->createProduct(['harga' => 10000]);
        $product2 = $this->createProduct(['harga' => 5000]);

        $transactionData = [
            'toko_id' => $toko->toko_id,
            'items' => [
                ['product_id' => $product1->product_id, 'qty' => 2], // 20000
                ['product_id' => $product2->product_id, 'qty' => 1]  // 5000
            ]
        ];

        $response = $this->actingAsUser($kasir)
            ->postJson('/api/v1/transactions', $transactionData);

        $response->assertStatus(200);
        // Docs say 200 OK or 201? Docs example response says 200 OK.
        // But standard REST is 201. Let's adjust if it fails.
        // Wait, Controller returns: `return new TransactionResource($transaction);` inside `store`.
        // Resource conversion usually preserves status unless set? Default is 200? Or 201 if created?
        // Laravel Resources return 201 if using `Product::create`? No, Resource doesn't dictate status code.
        // If controller doesn't specify code, it's 200.
        // I will change assertion to assertStatus(201) because standard create is 201, but if it fails I'll check.
        // Actually, let's allow 200 or 201.
        
        $response->assertSuccessful();

        $this->assertDatabaseHas('trn_transaksi_toko', [
            'kasir_id' => $kasir->user_id,
            'toko_id' => $toko->toko_id,
            'total_harga' => 25000
        ]);

        $transaction = TrnTransaksiToko::where('kasir_id', $kasir->user_id)->first();
        
        $this->assertDatabaseHas('trn_transaksi_detail_toko', [
            'transaksi_id' => $transaction->transaksi_id,
            'product_id' => $product1->product_id,
            'qty' => 2,
            'price_at_moment' => 10000
        ]);
    }

    /** @test */
    public function test_create_transaction_wrong_toko()
    {
        $kasir = $this->createUser(['role' => 'kasir']);
        $toko = $this->createToko(); 
        // Kasir NOT assigned to tkoo

        $product = $this->createProduct();

        $transactionData = [
            'toko_id' => $toko->toko_id,
            'items' => [
                ['product_id' => $product->product_id, 'qty' => 1]
            ]
        ];

        $response = $this->actingAsUser($kasir)
            ->postJson('/api/v1/transactions', $transactionData);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_create_transaction_invalid_product()
    {
        $kasir = $this->createUser(['role' => 'kasir']);
        $toko = $this->createToko();
        MapUserToko::create([
            'user_id' => $kasir->user_id,
            'toko_id' => $toko->toko_id
        ]);

        $transactionData = [
            'toko_id' => $toko->toko_id,
            'items' => [
                ['product_id' => 'invalid-uuid', 'qty' => 1]
            ]
        ];

        $response = $this->actingAsUser($kasir)
            ->postJson('/api/v1/transactions', $transactionData);

        // Validation should fail
        $response->assertStatus(422);
    }

    /** @test */
    public function test_list_transactions_role_filter()
    {
        $kasir1 = $this->createUser(['role' => 'kasir']);
        $toko1 = $this->createToko();
        MapUserToko::create([
            'user_id' => $kasir1->user_id,
            'toko_id' => $toko1->toko_id
        ]);

        $kasir2 = $this->createUser(['role' => 'kasir']);
        $toko2 = $this->createToko();
        MapUserToko::create([
            'user_id' => $kasir2->user_id,
            'toko_id' => $toko2->toko_id
        ]); // Kasir2 assigned to toko2

        // Trans 1 in Toko 1
        $this->createTransaction($kasir1, $toko1);
        
        // Trans 2 in Toko 2
        $this->createTransaction($kasir2, $toko2);

        // Kasir 1 should only see Trans 1
        $response = $this->actingAsUser($kasir1)
            ->getJson('/api/v1/transactions');
            
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data'); // Should be 1
            
        // Superadmin should see both
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $responseSA = $this->actingAsUser($superadmin)
             ->getJson('/api/v1/transactions');
             
        $responseSA->assertStatus(200);
        // Assuming pagination per_page is 10 and we have 2
        $this->assertGreaterThanOrEqual(2, count($responseSA->json('data')));
    }

    /** @test */
    public function test_toko_summary_data()
    {
        $superadmin = $this->createUser(['role' => 'superadmin']);
        $toko = $this->createToko();
        $kasir = $this->createUser(['role' => 'kasir']);
        
        $this->createTransaction($kasir, $toko, 10000);
        $this->createTransaction($kasir, $toko, 20000);

        $response = $this->actingAsUser($superadmin)
            ->getJson('/api/v1/transactions/summary');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'toko_id' => $toko->toko_id,
                'total_transactions' => 2,
                'total_revenue' => 30000
            ]);
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

    protected function createTransaction($user, $toko, $total = 10000)
    {
        return TrnTransaksiToko::create([
            'kasir_id' => $user->user_id,
            'toko_id' => $toko->toko_id,
            'total_harga' => $total,
            'created_by' => 'system'
        ]);
    }
}
