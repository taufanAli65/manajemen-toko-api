<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle login request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        if (!$token) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'token' => $token
        ]);
    }

    /**
     * Handle logout request.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user.
     *
     * @return JsonResponse|UserResource
     */
    public function me()
    {
        $user = $this->authService->getAuthenticatedUser();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        return new UserResource($user);
    }

    /**
     * Handle user registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return UserResource
     */
    public function register(RegisterRequest $request): UserResource
    {
        $userRole = $request->auth_user->role;
        // Handle Enum scenario if role is casted to Enum
        $roleValue = $userRole instanceof \App\Enums\UserRole ? $userRole->value : $userRole;

        if ($roleValue === 'superadmin') {
            $user = $this->authService->register($request->only([
                'full_name',
                'email',
                'password',
                'role',
            ]));
        } else {
            $data = $request->only([
                'full_name',
                'email',
                'password',
            ]);
            $data['role'] = 'kasir';
            $user = $this->authService->register($data);
        }

        return new UserResource($user);
    }

    /**
     * Handle user update.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $user_id
     * @return UserResource|JsonResponse
     */
    public function update(UpdateUserRequest $request, string $user_id)
    {
        $authUser = $request->auth_user;
        $userRole = $authUser->role;
        $roleValue = $userRole instanceof \App\Enums\UserRole ? $userRole->value : $userRole;

        $data = $request->only([
            'full_name',
            'email',
            'password',
            'role',
        ]);

        if ($authUser->user_id !== $user_id && !in_array($roleValue, ['superadmin', 'admin'])) {
            return response()->json([
                'message' => 'Forbidden: You can only update your own profile'
            ], 403);
        }

        if ($roleValue == 'superadmin') {
            $data['role'] = $request->input('role', null);
        } else if ($roleValue == 'admin') {
            $data['role'] = 'kasir';
        } else {
            unset($data['role']);
        }

        // Filter out null role if it wasn't set or unset
        if (array_key_exists('role', $data) && is_null($data['role'])) {
            unset($data['role']);
        }

        $user = $this->authService->updateUser($user_id, $data);

        if (!$user) {
            return response()->json([
                'message' => 'User not found or update failed'
            ], 404);
        }

        return new UserResource($user);
    }

    public function listUsers(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $role = $request->input('role');
        $tokoId = $request->input('toko_id');
        $search = $request->input('search');
        
        $users = $this->authService->listUsers($role, $tokoId, $perPage, $search);
        
        return UserResource::collection($users);
    }
}