<?php

namespace App\Http\Controllers\API;

use App\Events\EventType;
use App\Events\ProductChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateImageRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\Product\ProductImageResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Services\ProductService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(string $shoppingListId)
    {
        $shoppingList = ShoppingList::find($shoppingListId);
        if (!$shoppingList) {
            throw new ModelNotFoundException('Shopping list by ID: ' . $shoppingListId . ' not found');
        }

        Gate::authorize('groupMember', $shoppingList->group);

        $products = $shoppingList->products;

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(ProductStoreRequest $request, string $shoppingListId)
    {
        $data = $request->validated();

        $shoppingList = ShoppingList::find($shoppingListId);
        if (!$shoppingList) {
            throw new ModelNotFoundException('Shopping list by ID: ' . $shoppingListId . ' not found');
        }

        Gate::authorize('groupMember', $shoppingList->group);

        $product = $shoppingList->products()->create($data);
        $product->shopping_list_id = $shoppingListId;
        $product->status = 0;
        $product->save();

        broadcast(new ProductChanged($product, EventType::Create))->toOthers();

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $shoppingListId, string $id)
    {
        $product = $this->productService->getProductByShoppingListId($shoppingListId, $id);

        Gate::authorize('groupMember', $product->shoppingList->group);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, string $shoppingListId, string $id)
    {
        $data = $request->validated();

        $product = $this->productService->getProductByShoppingListId($shoppingListId, $id);

        Gate::authorize('groupMember', $product->shoppingList->group);

        $product->update($data);
        if (isset($data['status'])) {
            if ($data['status'] !== Product::NONE_STATUS) {
                $product->buyer_id = Auth::id();
            } else {
                $product->buyer_id = null;
            }
        }
        $product->save();

        broadcast(new ProductChanged($product, EventType::Update))->toOthers();

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $shoppingListId, string $id)
    {
        $product = $this->productService->getProductByShoppingListId($shoppingListId, $id);

        Gate::authorize('groupMember', $product->shoppingList->group);

        $product->delete();

        broadcast(new ProductChanged($product, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }

    public function showImage(string $shoppingListId, string $productId)
    {
        $product = $this->productService->getProductByShoppingListId($shoppingListId, $productId);

        Gate::authorize('groupMember', $product->shoppingList->group);

        return new ProductImageResource($product);
    }

    public function updateImage(ProductUpdateImageRequest $request, string $shoppingListId, string $productId)
    {
        $data = $request->validated();

        $product = $this->productService->getProductByShoppingListId($shoppingListId, $productId);

        Gate::authorize('groupMember', $product->shoppingList->group);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $path = $file->storeAs('public/products', time() . '.' . $extension);
            $data['image'] = $path;
        }

        $product->update($data);

        broadcast(new ProductChanged($product, EventType::Update))->toOthers();

        return new ProductImageResource($product);
    }

    public function destroyImage(string $shoppingListId, string $productId): JsonResponse
    {
        $product = $this->productService->getProductByShoppingListId($shoppingListId, $productId);

        Gate::authorize('groupMember', $product->shoppingList->group);

        $product->update(['image' => null]);

        broadcast(new ProductChanged($product, EventType::Delete))->toOthers();

        return response()->json(null, 204);
    }
}
