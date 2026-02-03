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

    /** 
     * Create a new user. Only superadmin can create users.
     *
     * @param array $data
     * @return MstUser
     */
    public function create(array $data): MstUser;

    /**
     * Update an existing user.
     *
     * @param string $userId
     * @param array $data
     * @return MstUser|null
     */
    public function update(string $userId, array $data): ?MstUser;

    /**
     * List all users based on role and toko.
     * Supports filtering by:
     * - All users (no filters)
     * - By role only
     * - By toko only
     * - By both role and toko
     * 
     * @param string|null $role
     * @param string|null $tokoId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listUsers(?string $role = null, ?string $tokoId = null, int $perPage = 10, ?string $search = null);
}
