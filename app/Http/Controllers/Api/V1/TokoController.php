<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTokoRequest;
use App\Http\Requests\UpdateTokoRequest;
use App\Http\Requests\AssignTokoRequest;
use App\Http\Resources\TokoResource;
use App\Http\Resources\UserResource;
use App\Services\TokoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    protected TokoService $tokoService;

    public function __construct(TokoService $tokoService)
    {
        $this->tokoService = $tokoService;
    }

    /**
     * List all tokos (paginated).
     * Accessible by: superadmin, admin
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $tokos = $this->tokoService->listAllTokos($perPage);
        
        return TokoResource::collection($tokos);
    }

    /**
     * Create a new toko.
     * Accessible by: superadmin only
     *
     * @param CreateTokoRequest $request
     * @return TokoResource
     */
    public function store(CreateTokoRequest $request): TokoResource
    {
        $toko = $this->tokoService->createToko($request->validated());
        
        return new TokoResource($toko);
    }

    /**
     * Get a single toko by ID.
     * Accessible by: all authenticated users
     *
     * @param string $tokoId
     * @return TokoResource|JsonResponse
     */
    public function show(string $tokoId)
    {
        $toko = $this->tokoService->findTokoById($tokoId);
        
        if (!$toko) {
            return response()->json([
                'message' => 'Toko not found'
            ], 404);
        }
        
        return new TokoResource($toko);
    }

    /**
     * Update an existing toko.
     * Accessible by: superadmin only
     *
     * @param UpdateTokoRequest $request
     * @param string $tokoId
     * @return TokoResource|JsonResponse
     */
    public function update(UpdateTokoRequest $request, string $tokoId)
    {
        $toko = $this->tokoService->updateToko($tokoId, $request->validated());
        
        if (!$toko) {
            return response()->json([
                'message' => 'Toko not found or update failed'
            ], 404);
        }
        
        return new TokoResource($toko);
    }

    /**
     * Soft delete a toko.
     * Accessible by: superadmin only
     *
     * @param string $tokoId
     * @return JsonResponse
     */
    public function destroy(string $tokoId): JsonResponse
    {
        $deleted = $this->tokoService->deleteTokoById($tokoId);
        
        if (!$deleted) {
            return response()->json([
                'message' => 'Toko not found or delete failed'
            ], 404);
        }
        
        return response()->json([
            'message' => 'Toko deleted successfully'
        ]);
    }

    /**
     * Assign a user to a toko.
     * Accessible by: superadmin, admin
     *
     * @param Request $request
     * @param string $tokoId
     * @return JsonResponse
     */
    public function assignUser(Request $request, string $tokoId): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:mst_user,user_id',
        ]);

        $assignment = $this->tokoService->assignTokoToUser($validated['user_id'], $tokoId);
        
        return response()->json([
            'message' => 'User assigned to toko successfully',
            'data' => $assignment
        ], 201);
    }

    /**
     * Remove a user from a toko.
     * Accessible by: superadmin, admin
     *
     * @param string $tokoId
     * @param string $userId
     * @return JsonResponse
     */
    public function removeUser(string $tokoId, string $userId): JsonResponse
    {
        $removed = $this->tokoService->removeTokoFromUser($userId, $tokoId);
        
        if (!$removed) {
            return response()->json([
                'message' => 'Assignment not found or removal failed'
            ], 404);
        }
        
        return response()->json([
            'message' => 'User removed from toko successfully'
        ]);
    }

    /**
     * List all users assigned to a toko (paginated).
     * Accessible by: superadmin, admin
     *
     * @param Request $request
     * @param string $tokoId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function listUsers(Request $request, string $tokoId)
    {
        $perPage = $request->input('per_page', 10);
        $users = $this->tokoService->listUsersByToko($tokoId, $perPage);
        
        return UserResource::collection($users);
    }

    /**
     * Get all tokos assigned to the authenticated user (paginated).
     * Accessible by: all authenticated users
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function myTokos(Request $request)
    {
        $authUser = $request->auth_user;
        $perPage = $request->input('per_page', 10);
        
        $tokos = $this->tokoService->listTokosByUser($authUser->user_id, $perPage);
        
        return TokoResource::collection($tokos);
    }
}
