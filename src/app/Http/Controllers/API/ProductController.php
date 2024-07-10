<?php

namespace App\Http\Controllers\API;

use App\Events\EventType;
use App\Events\ProductChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ShoppingList;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(string $shoppingListId)
    {
        $this->authorize('viewAny', [Product::class, $shoppingListId]);

        $shoppingList = ShoppingList::findOrFail($shoppingListId);
        $products = $shoppingList->products;

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(ProductStoreRequest $request, string $shoppingListId)
    {
        $this->authorize('create', [Product::class, $shoppingListId]);

        $shoppingList = ShoppingList::findOrFail($shoppingListId);
        $product = $shoppingList->products()->create($request->validated());
        $product['shopping_list_id'] = $shoppingListId;

        broadcast(new ProductChanged($product, EventType::Create))->toOthers();

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(string $shoppingListId, string $id)
    {
        $product = Product::where('shopping_list_id', $shoppingListId)->findOrFail($id);
        $this->authorize('view', $product);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(ProductUpdateRequest $request, string $shoppingListId, string $id)
    {
        $product = Product::where('shopping_list_id', $shoppingListId)->findOrFail($id);
        $this->authorize('update', $product);

        $product->update($request->validated());

        broadcast(new ProductChanged($product, EventType::Update))->toOthers();

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(string $shoppingListId, string $id)
    {
        $product = Product::where('shopping_list_id', $shoppingListId)->findOrFail($id);
        $this->authorize('delete', $product);

        $product->delete();

        broadcast(new ProductChanged($product, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }
}
