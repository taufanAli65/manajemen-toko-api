<?php

namespace App\Repositories\Contracts;

use App\Models\MstUser;

interface UserRepositoryInterface
{
    /**
     * Find a user by email address.
     *
     * @param string $email
     * @return MstUser|null
     */
    public function findByEmail(string $email): ?MstUser;

    /**
     * Find a user by ID.
     *
     * @param string $userId
     * @return MstUser|null
     */
    public function findById(string $userId): ?MstUser;
}
