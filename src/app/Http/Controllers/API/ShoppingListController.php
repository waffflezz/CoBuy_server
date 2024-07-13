<?php

namespace App\Http\Controllers\API;

use App\Events\EventType;
use App\Events\ListChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\List\ShoppingListStoreRequest;
use App\Http\Requests\List\ShoppingListUpdateRequest;
use App\Http\Resources\ShoppingListResource;
use App\Models\Group;
use App\Models\ShoppingList;
use App\Services\ShoppingListService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShoppingListController extends Controller
{
    use AuthorizesRequests;

    private ShoppingListService $shoppingListService;

    public function __construct(ShoppingListService $shoppingListService)
    {
        $this->shoppingListService = $shoppingListService;
    }

    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $group_id = $request->query('group_id');

        if (!is_numeric($group_id)) {
            throw new BadRequestHttpException('group_id must be numeric');
        }

        $user = Auth::user();

        if (! $user->groups->pluck('id')->contains($group_id)) {
            throw new BadRequestHttpException('group_id not allowed');
        }

        $this->authorize('groupMember', [$user, Group::find($group_id)]);

        $shoppingLists = ShoppingList::where('group_id', $group_id)->get();

        return ShoppingListResource::collection($shoppingLists);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(ShoppingListStoreRequest $request)
    {
        $data = $request->validated();

        $user = Auth::user();

        $group_id = $data['group_id'];
        $group = $user->groups()->find($group_id);
        if (!$group) {
            throw new ModelNotFoundException('Group by ID: ' . $group_id . ' not found');
        }

        $this->authorize('groupMember', [$user,  $group]);

        $shoppingList = $group->shoppingLists()->create($data);

        broadcast(new ListChanged($shoppingList, EventType::Create))->toOthers();

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $shoppingList = $this->shoppingListService->getShoppingList($user, $id);

        $this->authorize('groupMember', [$user, $shoppingList->group()]);

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(ShoppingListUpdateRequest $request, string $id)
    {
        $data = $request->validated();

        $user = Auth::user();
        $shoppingList = $this->shoppingListService->getShoppingList($user, $id);

        $this->authorize('groupMember', [$user, $shoppingList->group()]);

        $shoppingList->update($data);

        broadcast(new ListChanged($shoppingList, EventType::Update))->toOthers();

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $shoppingList = $this->shoppingListService->getShoppingList($user, $id);

        $this->authorize('groupMember', [$user, $shoppingList->group()]);

        $shoppingList->delete();

        broadcast(new ListChanged($shoppingList, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }
}
