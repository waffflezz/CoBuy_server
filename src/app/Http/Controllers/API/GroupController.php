<?php

namespace App\Http\Controllers\API;

use App\Events\EventType;
use App\Events\GroupChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GroupController extends Controller
{
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
     */
    public function show(string $id): GroupResource
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupStoreRequest $request, string $id): GroupResource
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);

        $group->update($request->validated());

        broadcast(new GroupChanged($group, EventType::Update))->toOthers();

        return new GroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);
        $group->delete();

        broadcast(new GroupChanged($group, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }

    public function leave(string $id)
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($id);
        $group->users()->detach($user->id);
        return response()->json(null, 204);
    }

    public function kick(string $groupId, string $userId)
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($groupId);

        if (!$group->users()->where('users.id', $userId)->exists()) {
            return response()->json(['error' => 'User does not exist'], 404);
        }

        $group->users()->detach($userId);
        return response()->json(null, 204);
    }
}
