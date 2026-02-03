<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTokoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'jenis_toko' => ['required', Rule::in(['pusat', 'cabang', 'retail'])],
            'admin_email' => 'required|email|unique:mst_user,email',
            'kasir_email' => 'required|email|unique:mst_user,email',
        ];
    }

    public function messages(): array
    {
        return [
            'admin_email.required' => 'Admin email is required',
            'admin_email.email' => 'Admin email must be a valid email address',
            'admin_email.unique' => 'Admin email already exists',
            'kasir_email.required' => 'Kasir email is required',
            'kasir_email.email' => 'Kasir email must be a valid email address',
            'kasir_email.unique' => 'Kasir email already exists',
        ];
    }
}
