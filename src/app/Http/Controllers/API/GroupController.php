<?php

namespace App\Http\Controllers\API;

use App\Events\EventType;
use App\Events\GroupChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Requests\Group\GroupUpdateImageRequest;
use App\Http\Resources\Group\GroupImageResource;
use App\Http\Resources\Group\GroupResource;
use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        $groups = $user->groups()->latest()->get();

        return GroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupStoreRequest $request): GroupResource
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $path = $file->storeAs('public/groups', time() . '.' . $extension);
            $data['image'] = $path;
        }

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

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $path = $file->storeAs('public/groups', time() . '.' . $extension);
            $data['image'] = $path;
        }

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

    public function leave(string $id): JsonResponse
    {
        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        $group->users()->detach($user->id);
        return response()->json(null, 204);
    }

    /**
     * @throws AuthorizationException
     */
    public function kick(Request $request)
    {
        $data = $request->validate([
            'groupId' => 'required|exists:groups,id',
            'userId' => 'required|exists:users,id'
        ]);

        $user = Auth::user();
        $group = $this->groupService->getGroupByUser($user, $data['groupId']);

        Gate::authorize('groupOwner', $group);

        if (!$group->users()->where('users.id', $data['userId'])->exists()) {
            return new ModelNotFoundException('User ' . $user->name . ' not found');
        }

        $group->users()->detach($data['userId']);
        return response()->json(null, 204);
    }

    public function showImage(string $id)
    {
        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        Gate::authorize('groupMember', $group);

        return new GroupImageResource($group);
    }

    public function updateImage(GroupUpdateImageRequest $request, string $id)
    {
        $data = $request->validated();

        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        Gate::authorize('groupOwner', $group);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $path = $file->storeAs('public/groups', time() . '.' . $extension);
            $data['image'] = $path;
        }

        $group->update($data);

        broadcast(new GroupChanged($group, EventType::Update))->toOthers();

        return new GroupImageResource($group);
    }

    public function destroyImage(string $id): JsonResponse
    {
        $user = Auth::user();

        $group = $this->groupService->getGroupByUser($user, $id);

        Gate::authorize('groupOwner', $group);

        $group->update(['image' => null]);

        broadcast(new GroupChanged($group, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }
}
