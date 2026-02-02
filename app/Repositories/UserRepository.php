<?php

namespace App\Repositories;

use App\Models\MstUser;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return MstUser|null
     */
    public function findByEmail(string $email): ?MstUser
    {
        return MstUser::where('email', $email)
            ->where('is_deleted', false)
            ->first();
    }

    /**
     * Find a user by ID.
     *
     * @param string $userId
     * @return MstUser|null
     */
    public function findById(string $userId): ?MstUser
    {
        return MstUser::where('user_id', $userId)
            ->where('is_deleted', false)
            ->first();
    }
}
