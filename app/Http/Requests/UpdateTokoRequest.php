<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTokoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'jenis_toko' => ['sometimes', Rule::in(['pusat', 'cabang', 'retail'])],
        ];
    }
}
