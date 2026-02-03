<?php

namespace App\Http\Requests;

class RegisterRequest extends BaseUserRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:mst_user,email',
            'password' => 'required|min:8',
            'full_name' => 'required|string|max:255',
            'role' => 'sometimes|in:superadmin,admin,kasir',
        ];
    }
}