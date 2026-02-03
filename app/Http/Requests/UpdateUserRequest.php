<?php

namespace App\Http\Requests;

class UpdateUserRequest extends BaseUserRequest
{
    public function rules(): array
    {
        $userId = $this->route('user_id');

        return [
            'email' => 'sometimes|email|unique:mst_user,email,' . $userId . ',user_id',
            'password' => 'sometimes|min:8',
            'full_name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:superadmin,admin,kasir',
        ];
    }
}