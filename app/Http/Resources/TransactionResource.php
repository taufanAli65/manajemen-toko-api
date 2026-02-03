<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'kasir' => [
                'user_id' => $this->kasir->user_id,
                'full_name' => $this->kasir->full_name,
                'email' => $this->kasir->email,
            ],
            'toko' => [
                'toko_id' => $this->toko->toko_id,
                'name' => $this->toko->name,
                'address' => $this->toko->address,
                'jenis_toko' => $this->toko->jenis_toko,
            ],
            'total_harga' => $this->total_harga,
            'items' => TransactionDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
