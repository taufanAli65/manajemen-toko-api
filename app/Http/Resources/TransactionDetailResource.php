<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => [
                'product_id' => $this->product->product_id,
                'name' => $this->product->name,
                'merk' => $this->product->merk,
            ],
            'qty' => $this->qty,
            'price_at_moment' => $this->price_at_moment,
            'subtotal' => $this->qty * $this->price_at_moment,
        ];
    }
}
