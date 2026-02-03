<?php

namespace App\Repositories;

use App\Models\TrnTransaksiToko;
use App\Models\TrnTransaksiDetailToko;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * Create a new transaction.
     * 
     * @param array $data
     * @return \App\Models\TrnTransaksiToko
     */
    public function createTransaction(array $data)
    {
        return TrnTransaksiToko::create([
            'kasir_id' => $data['kasir_id'],
            'toko_id' => $data['toko_id'],
            'total_harga' => $data['total_harga'],
            'created_by' => $data['created_by'],
        ]);
    }

    /**
     * Create a transaction detail item.
     * 
     * @param array $data
     * @return \App\Models\TrnTransaksiDetailToko
     */
    public function createTransactionDetail(array $data)
    {
        return TrnTransaksiDetailToko::create([
            'transaksi_id' => $data['transaksi_id'],
            'product_id' => $data['product_id'],
            'qty' => $data['qty'],
            'price_at_moment' => $data['price_at_moment'],
            'created_by' => $data['created_by'],
        ]);
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
        $query = TrnTransaksiToko::with(['kasir', 'toko', 'details.product'])
            ->where('is_deleted', false);

        // Filter by date range
        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Filter by toko_id
        if (isset($filters['toko_id'])) {
            $query->where('toko_id', $filters['toko_id']);
        }

        // Filter by kasir_id
        if (isset($filters['kasir_id'])) {
            $query->where('kasir_id', $filters['kasir_id']);
        }

        // Filter by toko_ids (for admin/kasir with multiple assigned tokos)
        if (isset($filters['toko_ids']) && is_array($filters['toko_ids'])) {
            $query->whereIn('toko_id', $filters['toko_ids']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get toko summary data.
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getTokoSummary(array $filters = [])
    {
        $query = TrnTransaksiToko::select(
                'toko_id',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(total_harga) as total_revenue')
            )
            ->where('is_deleted', false);

        // Filter by date range
        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Filter by toko_id
        if (isset($filters['toko_id'])) {
            $query->where('toko_id', $filters['toko_id']);
        }

        // Filter by toko_ids (for admin/kasir with multiple assigned tokos)
        if (isset($filters['toko_ids']) && is_array($filters['toko_ids'])) {
            $query->whereIn('toko_id', $filters['toko_ids']);
        }

        return $query->groupBy('toko_id')
            ->with('toko')
            ->get();
    }

    /**
     * Find a transaction by ID.
     * 
     * @param string $transactionId
     * @return \App\Models\TrnTransaksiToko|null
     */
    public function findTransactionById(string $transactionId)
    {
        return TrnTransaksiToko::with(['kasir', 'toko', 'details.product'])
            ->where('transaksi_id', $transactionId)
            ->where('is_deleted', false)
            ->first();
    }
}
