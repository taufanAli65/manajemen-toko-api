<?php

namespace App\Repositories;

use App\Models\MapUserToko;
use App\Models\MstUser;
use App\Models\MstToko;
use App\Repositories\Contracts\TokoRepositoryInterface;

class TokoRepository implements TokoRepositoryInterface
{
    /**
     * Assign a toko to a user.
     *
     * @param string $userId
     * @param string $tokoId
     * @return MapUserToko
     */
    public function assignTokoToUser(string $userId, string $tokoId): MapUserToko
    {
        return MapUserToko::create([
            'user_id' => $userId,
            'toko_id' => $tokoId,
        ]);
    }

    /**
     * Remove a toko assignment from a user.
     *
     * @param string $userId
     * @param string $tokoId
     * @return bool
     */
    public function removeTokoFromUser(string $userId, string $tokoId): bool
    {
        return MapUserToko::where('user_id', $userId)
            ->where('toko_id', $tokoId)
            ->delete() > 0;
    }

    /**
     * List all tokos assigned to a user.
     *
     * @param string $userId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listTokoByUser(string $userId, int $perPage = 10, ?string $search = null)
    {
        $query = MapUserToko::join('mst_toko', 'map_user_toko.toko_id', '=', 'mst_toko.toko_id')
            ->where('map_user_toko.user_id', $userId)
            ->where('mst_toko.is_deleted', false);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mst_toko.name', 'LIKE', "%{$search}%")
                  ->orWhere('mst_toko.address', 'LIKE', "%{$search}%");
            });
        }

        return $query->select(
                'mst_toko.name', 
                'mst_toko.address', 
                'mst_toko.jenis_toko',
                'map_user_toko.toko_id'
            )
            ->paginate($perPage);
    }

    /**
     * List all users based on toko.
     * 
     * @param string|null $tokoId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listUserByToko(?string $tokoId = null, int $perPage = 10, ?string $search = null)
    {
        $query = MapUserToko::join('mst_user', 'map_user_toko.user_id', '=', 'mst_user.user_id')
            ->where('map_user_toko.toko_id', $tokoId)
            ->where('mst_user.is_deleted', false);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('mst_user.full_name', 'LIKE', "%{$search}%")
                  ->orWhere('mst_user.email', 'LIKE', "%{$search}%");
            });
        }

        return $query->select(
                'mst_user.user_id',
                'mst_user.full_name',
                'mst_user.role'
            )
            ->paginate($perPage);
    }

    /**
     * List all tokos.
     * 
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllTokos(int $perPage = 10, ?string $search = null)
    {
        $query = MstToko::where('is_deleted', false);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new toko.
     * 
     * @param array $data
     * @return \App\Models\MstToko
     */
    public function createToko(array $data)
    {
        return MstToko::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'jenis_toko' => $data['jenis_toko'],
        ]);
    }

    /**
     * Update an existing toko.
     * 
     * @param string $tokoId
     * @param array $data
     * @return \App\Models\MstToko|null
     */
    public function updateToko(string $tokoId, array $data)
    {
        $toko = MstToko::find($tokoId);
        if (!$toko) {
            return null;
        }
        if (isset($data['name'])) {
            $toko->name = $data['name'];
        }
        if (isset($data['address'])) {
            $toko->address = $data['address'];
        }
        if (isset($data['jenis_toko'])) {
            $toko->jenis_toko = $data['jenis_toko'];
        }
        $toko->save();
        return $toko;
    }

    /**
     * Find a toko by ID.
     * 
     * @param string $tokoId
     * @return \App\Models\MstToko|null
     */
    public function findTokoById(string $tokoId)
    {
        return MstToko::where('toko_id', $tokoId)
            ->where('is_deleted', false)
            ->first();
    }

    /**
     * Delete a toko by ID.
     * 
     * @param string $tokoId
     * @return bool
     */    public function deleteTokoById(string $tokoId): bool
    {
        $toko = MstToko::find($tokoId);
        if (!$toko) {
            return false;
        }
        $toko->is_deleted = true;
        $toko->save();
        $toko->delete();
        return true;
    }
}