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

    /**
     * Create a new user. Only superadmin can create users.
     *
     * @param array $data
     * @return MstUser
     */
    public function create(array $data): MstUser
    {
        return MstUser::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'],
            'role' => $data['role'],
        ]);
    }

    /**
     * Update an existing user.
     *
     * @param string $userId
     * @param array $data
     * @return MstUser|null
     */
    public function update(string $userId, array $data): ?MstUser
    {
        $user = $this->findById($userId);
        if (!$user) {
            return null;
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

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
    public function listUsers(?string $role = null, ?string $tokoId = null, int $perPage = 10, ?string $search = null)
    {
        $query = MstUser::query()
            ->select('mst_user.user_id', 'mst_user.full_name', 'mst_user.email', 'mst_user.role')
            ->where('mst_user.is_deleted', false);

        if ($role) {
            $query->where('mst_user.role', $role);
        }

        if ($tokoId) {
            $query->join('map_user_toko', 'mst_user.user_id', '=', 'map_user_toko.user_id')
                ->where('map_user_toko.toko_id', $tokoId)
                ->distinct();
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mst_user.full_name', 'LIKE', "%{$search}%")
                  ->orWhere('mst_user.email', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }
}