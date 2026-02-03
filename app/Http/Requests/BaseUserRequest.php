<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get common validation rules for user data.
     *
     * @return array
     */
    protected function commonRules(): array
    {
        return [
            'email' => 'email',
            'password' => 'min:8',
            'full_name' => 'string|max:255',
            'role' => 'in:superadmin,admin,kasir',
        ];
    }
}
