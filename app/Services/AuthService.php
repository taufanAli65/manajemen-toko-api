<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Attempt to authenticate a user and return a JWT token.
     *
     * @param string $email
     * @param string $password
     * @return string|null Returns JWT token on success, null on failure
     */
    public function login(string $email, string $password): ?string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        return JWTAuth::fromUser($user);
    }

    /**
     * Get the authenticated user from JWT token.
     *
     * @return \App\Models\MstUser|null
     */
    public function getAuthenticatedUser()
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Invalidate the current JWT token.
     *
     * @return bool
     */
    public function logout(): bool
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    public function updateUser(string $userId, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->update($userId, $data);
    }

    /**
     * List all users.
     *
     * @param string|null $role
     * @param string|null $tokoId
     * @param int $perPage
     * @param string|null $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listUsers(?string $role = null, ?string $tokoId = null, int $perPage = 10, ?string $search = null)
    {
        return $this->userRepository->listUsers($role, $tokoId, $perPage, $search);
    }
}
