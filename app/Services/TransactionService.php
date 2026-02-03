<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionService
{
    protected TransactionRepositoryInterface $transactionRepository;
    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Create a new transaction with details.
     * 
     * @param array $data
     * @param string $kasirId
     * @return \App\Models\TrnTransaksiToko
     * @throws \Exception
     */
    public function createTransaction(array $data, string $kasirId)
    {
        return DB::transaction(function () use ($data, $kasirId) {
            $totalHarga = 0;
            $processedItems = [];

            foreach ($data['items'] as $item) {
                $product = $this->productRepository->findProductById($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Product not found: {$item['product_id']}");
                }

                $subtotal = $product->harga * $item['qty'];
                $totalHarga += $subtotal;

                $processedItems[] = [
                    'product_id' => $product->product_id,
                    'qty' => $item['qty'],
                    'price_at_moment' => $product->harga,
                ];
            }

            $transaction = $this->transactionRepository->createTransaction([
                'kasir_id' => $kasirId,
                'toko_id' => $data['toko_id'],
                'total_harga' => $totalHarga,
                'created_by' => $kasirId,
            ]);

            foreach ($processedItems as $item) {
                $this->transactionRepository->createTransactionDetail([
                    'transaksi_id' => $transaction->transaksi_id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price_at_moment' => $item['price_at_moment'],
                    'created_by' => $kasirId,
                ]);
            }

            return $this->transactionRepository->findTransactionById($transaction->transaksi_id);
        });
    }

    /**
     * List transactions with filters.
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listTransactions(array $filters = [], int $perPage = 10)
    {
        $filters = $this->applyDefaultDateRange($filters);

        return $this->transactionRepository->listTransactions($filters, $perPage);
    }

    /**
     * Get toko summary data.
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getTokoSummary(array $filters = [])
    {
        $filters = $this->applyDefaultDateRange($filters);

        return $this->transactionRepository->getTokoSummary($filters);
    }

    /**
     * Get user's assigned toko IDs.
     * 
     * @param \App\Models\MstUser $user
     * @return array
     */
    public function getUserTokoIds($user): array
    {
        return $user->tokos()->pluck('mst_toko.toko_id')->toArray();
    }

    /**
     * Check if user has access to a specific toko.
     * 
     * @param \App\Models\MstUser $user
     * @param string $tokoId
     * @return bool
     */
    public function userHasAccessToToko($user, string $tokoId): bool
    {
        return $user->tokos()->where('mst_toko.toko_id', $tokoId)->exists();
    }

    /**
     * Apply default date range (today) if not provided.
     * 
     * @param array $filters
     * @return array
     */
    private function applyDefaultDateRange(array $filters): array
    {
        if (!isset($filters['start_date']) && !isset($filters['end_date'])) {
            $filters['start_date'] = Carbon::today()->toDateString();
            $filters['end_date'] = Carbon::today()->toDateString();
        }

        return $filters;
    }
}
