<?php

namespace App\Http\Controllers\API;

use App\Events\EventType;
use App\Events\ListChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\List\ShoppingListStoreRequest;
use App\Http\Requests\List\ShoppingListUpdateRequest;
use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $group_id = $request->query('group_id');

        if (!is_numeric($group_id)) {
            return response()->json(['error' => 'group_id must be numeric'], 400);
        }

        $user = Auth::user();

        if (! $user->groups->pluck('id')->contains($group_id)) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $shoppingLists = ShoppingList::where('group_id', $group_id)->get();

        return ShoppingListResource::collection($shoppingLists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShoppingListStoreRequest $request)
    {
        $user = Auth::user();
        $group = $user->groups()->findOrFail($request->group_id);

        $shoppingList = $group->shoppingLists()->create($request->validated());

        broadcast(new ListChanged($shoppingList, EventType::Create))->toOthers();

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $shoppingList = ShoppingList::whereIn('group_id', $user->groups->pluck('id'))->findOrFail($id);

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShoppingListUpdateRequest $request, string $id)
    {
        $user = Auth::user();
        $shoppingList = ShoppingList::whereIn('group_id', $user->groups->pluck('id'))->findOrFail($id);

        $shoppingList->update($request->validated());

        broadcast(new ListChanged($shoppingList, EventType::Update))->toOthers();

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $shoppingList = ShoppingList::whereIn('group_id', $user->groups->pluck('id'))->findOrFail($id);

        $shoppingList->delete();

        broadcast(new ListChanged($shoppingList, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }
}
