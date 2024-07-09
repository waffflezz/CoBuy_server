<?php

namespace App\Http\Controllers\API;

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
    public function index()
    {
        $user = Auth::user();
        $shoppingLists = ShoppingList::whereIn('group_id', $user->groups->pluck('id'))->get();

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

        return response()->json(null, 204);
    }
}