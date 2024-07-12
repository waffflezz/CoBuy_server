<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ShoppingList;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductService
{
    public function getProductByShoppingListId(string $shoppingListId, string $productId): Product
    {
        $product = Product::where('shopping_list_id', $shoppingListId)->find($productId);
        if (!$product) {
            throw new ModelNotFoundException('Product by ID: ' . $productId . ' not found');
        }

        return $product;
    }
}
