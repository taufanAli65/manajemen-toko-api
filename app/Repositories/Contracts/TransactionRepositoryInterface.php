<?php

namespace App\Repositories\Contracts;

interface TransactionRepositoryInterface
{
    /**
     * Create a new transaction.
     * 
     * @param array $data
     * @return \App\Models\TrnTransaksiToko
     */
    public function createTransaction(array $data);

    /**
     * Create a transaction detail item.
     * 
     * @param array $data
     * @return \App\Models\TrnTransaksiDetailToko
     */
    public function createTransactionDetail(array $data);

    /**
     * List transactions with filters.
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listTransactions(array $filters = [], int $perPage = 10);

    /**
     * Get toko summary data.
     * 
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getTokoSummary(array $filters = []);

    /**
     * Find a transaction by ID.
     * 
     * @param string $transactionId
     * @return \App\Models\TrnTransaksiToko|null
     */
    public function findTransactionById(string $transactionId);
}
