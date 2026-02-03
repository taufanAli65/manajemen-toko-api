<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'toko_id' => 'required|string|exists:mst_toko,toko_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string|exists:mst_produk,product_id',
            'items.*.qty' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'toko_id.required' => 'Toko ID is required',
            'toko_id.exists' => 'Toko not found',
            'items.required' => 'Transaction items are required',
            'items.min' => 'At least one item is required',
            'items.*.product_id.required' => 'Product ID is required for each item',
            'items.*.product_id.exists' => 'Product not found',
            'items.*.qty.required' => 'Quantity is required for each item',
            'items.*.qty.min' => 'Quantity must be at least 1',
        ];
    }
}
