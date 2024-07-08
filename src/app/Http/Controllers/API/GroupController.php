<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return GroupResource::collection(Group::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupStoreRequest $request)
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
    public function show(string $id)
    {
        $group = Group::findOrFail($id);
        return new GroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupStoreRequest $request, string $id)
    {
        $group = Group::findOrFail($id);
        $group->update($request->validated());
        return new GroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Group::destroy($id);
        return response()->json(null, 204);
    }
}
