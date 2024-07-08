<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $user = Auth::user();
        $groups = $user->groups();

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
        return response()->json(null, 204);
    }
}
