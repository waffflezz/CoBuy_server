<?php

namespace App\Http\Controllers\API;

use App\Events\EventType;
use App\Events\GroupChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
    private GroupService $groupService;
    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $user = Auth::user();
        $groups = $user->groups;

        return GroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupStoreRequest $request): GroupResource
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::id();

        $group = Group::create($data);
        $group->users()->attach(Auth::id());

        broadcast(new GroupChanged($group, EventType::Create))->toOthers();

        return new GroupResource($group);
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(string $id): GroupResource
    {
        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        Gate::authorize('groupMember', $group);

        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(GroupStoreRequest $request, string $id): GroupResource
    {
        $data = $request->validated();

        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        Gate::authorize('groupOwner', $group);

        $group->update($data);

        broadcast(new GroupChanged($group, EventType::Update))->toOthers();

        return new GroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        Gate::authorize('groupOwner', $group);

        $group->delete();

        broadcast(new GroupChanged($group, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }

    public function leave(string $id)
    {
        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        $group->users()->detach($user->id);
        return response()->json(null, 204);
    }

    /**
     * @throws AuthorizationException
     */
    public function kick(string $groupId, string $userId)
    {
        $user = Auth::user();
        $group = $this->groupService->getGroupByUser($user, $groupId);

        Gate::authorize('groupOwner', $group);

        if (!$group->users()->where('users.id', $userId)->exists()) {
            return new ModelNotFoundException('User ' . $user->name . ' not found');
        }

        $group->users()->detach($userId);
        return response()->json(null, 204);
    }
}
