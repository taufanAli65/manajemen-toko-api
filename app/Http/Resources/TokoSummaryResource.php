<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokoSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'toko_id' => $this->toko_id,
            'toko_name' => $this->toko->name,
            'toko_address' => $this->toko->address,
            'jenis_toko' => $this->toko->jenis_toko,
            'total_transactions' => (int) $this->total_transactions,
            'total_revenue' => (int) $this->total_revenue,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];
    }
}
