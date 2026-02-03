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
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listTokosByUser(string $userId, int $perPage = 10, ?string $search = null)
    {
        return $this->tokoRepository->listTokoByUser($userId, $perPage, $search);
    }

    /**
     * List all users based on toko.
     * 
     * @param string|null $tokoId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listUsersByToko(?string $tokoId = null, int $perPage = 10, ?string $search = null)
    {
        return $this->tokoRepository->listUserByToko($tokoId, $perPage, $search);
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
        return $this->tokoRepository->listAllTokos($perPage, $search);
    }

    /**
     * Create a new toko with admin and kasir users.
     * 
     * @param array $data
     * @return \App\Models\MstToko
     * @throws \Exception
     */
    public function createToko(array $data)
    {
        return \DB::transaction(function () use ($data) {
            // Create the toko
            $toko = $this->tokoRepository->createToko([
                'name' => $data['name'],
                'address' => $data['address'],
                'jenis_toko' => $data['jenis_toko'],
            ]);

            // Generate default password (toko name without spaces + "123")
            $defaultPassword = \Hash::make(str_replace(' ', '', strtolower($data['name'])) . '123');

            // Create admin user
            $admin = \App\Models\MstUser::create([
                'email' => $data['admin_email'],
                'password' => $defaultPassword,
                'full_name' => 'Admin ' . $data['name'],
                'role' => \App\Enums\UserRole::ADMIN,
            ]);

            // Create kasir user
            $kasir = \App\Models\MstUser::create([
                'email' => $data['kasir_email'],
                'password' => $defaultPassword,
                'full_name' => 'Kasir ' . $data['name'],
                'role' => \App\Enums\UserRole::KASIR,
            ]);

            // Assign both users to the toko
            $this->tokoRepository->assignTokoToUser($admin->user_id, $toko->toko_id);
            $this->tokoRepository->assignTokoToUser($kasir->user_id, $toko->toko_id);

            return $toko;
        });
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