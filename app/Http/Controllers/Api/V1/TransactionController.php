<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Resources\TokoSummaryResource;
use App\Http\Resources\TransactionListResource;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Create a new transaction.
     * Accessible by: all authenticated users (kasir, admin, superadmin)
     * 
     * @param CreateTransactionRequest $request
     * @return TransactionResource|JsonResponse
     */
    public function store(CreateTransactionRequest $request)
    {
        $user = auth()->user();
        
        // Validate that user has access to the toko
        if ($user->role !== UserRole::SUPERADMIN) {
            if (!$this->transactionService->userHasAccessToToko($user, $request->toko_id)) {
                return response()->json([
                    'message' => 'Forbidden: You do not have access to this toko'
                ], 403);
            }
        }

        try {
            $transaction = $this->transactionService->createTransaction(
                $request->validated(),
                $user->user_id
            );

            return new TransactionResource($transaction);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Transaction creation failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * List transactions with filters.
     * Accessible by: superadmin (all transactions), admin/kasir (assigned tokos only)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 10);
        
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'toko_id' => $request->input('toko_id'),
        ];

        // For admin and kasir, filter by their assigned tokos
        if ($user->role !== UserRole::SUPERADMIN) {
            $tokoIds = $this->transactionService->getUserTokoIds($user);
            
            // If toko_id is specified, validate access
            if ($filters['toko_id']) {
                if (!in_array($filters['toko_id'], $tokoIds)) {
                    return response()->json([
                        'message' => 'Forbidden: You do not have access to this toko'
                    ], 403);
                }
            } else {
                // Filter by all assigned tokos
                $filters['toko_ids'] = $tokoIds;
            }
        }

        $transactions = $this->transactionService->listTransactions($filters, $perPage);
        
        return TransactionListResource::collection($transactions);
    }

    /**
     * Get toko summary data.
     * Accessible by: superadmin (all tokos), admin/kasir (assigned tokos only)
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function summary(Request $request)
    {
        $user = auth()->user();
        
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        // For admin and kasir, filter by their assigned tokos
        if ($user->role !== UserRole::SUPERADMIN) {
            $tokoIds = $this->transactionService->getUserTokoIds($user);
            $filters['toko_ids'] = $tokoIds;
        }

        $summary = $this->transactionService->getTokoSummary($filters);
        
        return TokoSummaryResource::collection($summary);
    }
}
