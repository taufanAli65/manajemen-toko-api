<?php

namespace App\Services;

use App\Repositories\Contracts\TokoRepositoryInterface;

class TokoService
{
    protected TokoRepositoryInterface $tokoRepository;

    public function __construct(TokoRepositoryInterface $tokoRepository)
    {
        $this->tokoRepository = $tokoRepository;
    }

    /**
     * Assign a toko to a user.
     *
     * @param string $userId
     * @param string $tokoId
     * @return \App\Models\MapUserToko
     */
    public function assignTokoToUser(string $userId, string $tokoId)
    {
        return $this->tokoRepository->assignTokoToUser($userId, $tokoId);
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
        return $this->tokoRepository->removeTokoFromUser($userId, $tokoId);
    }

    /**
     * List all tokos assigned to a user.
     *
     * @param string $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listTokosByUser(string $userId, int $perPage = 10)
    {
        return $this->tokoRepository->listTokoByUser($userId, $perPage);
    }

    /**
     * List all users based on toko.
     * 
     * @param string|null $tokoId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listUsersByToko(?string $tokoId = null, int $perPage = 10)
    {
        return $this->tokoRepository->listUserByToko($tokoId, $perPage);
    }

    /**
     * List all tokos.
     * 
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listAllTokos(int $perPage = 10)
    {
        return $this->tokoRepository->listAllTokos($perPage);
    }

    /**
     * Create a new toko.
     * 
     * @param array $data
     * @return \App\Models\MstToko
     */
    public function createToko(array $data)
    {
        return $this->tokoRepository->createToko($data);
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
        return $this->tokoRepository->updateToko($tokoId, $data);
    }

    /**
     * Find a toko by ID.
     * 
     * @param string $tokoId
     * @return \App\Models\MstToko|null
     */
    public function findTokoById(string $tokoId)
    {
        return $this->tokoRepository->findTokoById($tokoId);
    }

    /**
     * Delete a toko by ID.
     * 
     * @param string $tokoId
     * @return bool
     */    public function deleteTokoById(string $tokoId): bool
    {
        return $this->tokoRepository->deleteTokoById($tokoId);
    }
}