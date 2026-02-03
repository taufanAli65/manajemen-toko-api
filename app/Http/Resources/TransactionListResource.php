<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'transaksi_id' => $this->transaksi_id,
            'kasir_name' => $this->kasir->full_name,
            'toko_name' => $this->toko->name,
            'total_harga' => $this->total_harga,
            'items_count' => $this->details->count(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
