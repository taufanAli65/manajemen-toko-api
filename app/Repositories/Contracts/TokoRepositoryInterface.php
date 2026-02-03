<?php

namespace App\Repositories\Contracts;

use App\Models\MapUserToko;

interface TokoRepository
{
    /**
     * Assign a toko to a user.
     *
     * @param string $userId
     * @param string $tokoId
     * @return MapUserToko
     */
    public function assignTokoToUser(string $userId, string $tokoId): MapUserToko;

    /**
     * Remove a toko assignment from a user.
     *
     * @param string $userId
     * @param string $tokoId
     * @return bool
     */
    public function removeTokoFromUser(string $userId, string $tokoId): bool;

    /**
     * List all tokos assigned to a user.
     *
     * @param string $userId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listTokoByUser(string $userId, int $perPage = 10, ?string $search = null);

    /**
     * List all users based on toko.
     * 
     * @param string|null $tokoId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listUserByToko(?string $tokoId = null, int $perPage = 10, ?string $search = null);

    /**
     * List all tokos.
     * 
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllTokos(int $perPage = 10, ?string $search = null);

    /**
     * Create a new toko.
     * 
     * @param array $data
     * @return \App\Models\MstToko
     */
    public function createToko(array $data);

    /**
     * Update an existing toko.
     * 
     * @param string $tokoId
     * @param array $data
     * @return \App\Models\MstToko|null
     */
    public function updateToko(string $tokoId, array $data);

    /**
     * Find a toko by ID.
     * 
     * @param string $tokoId
     * @return \App\Models\MstToko|null
     */
    public function findTokoById(string $tokoId);

    /**
     * Delete a toko by ID.
     * 
     * @param string $tokoId
     * @return bool
     */
    public function deleteTokoById(string $tokoId): bool;
}