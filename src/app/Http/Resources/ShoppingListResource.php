<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'groupId' => $this->group_id,
            'productsCount' => $this->products->count(),
            'checkedProductsCount' => $this->products->filter(function (Product $product) {
                return $product->status != 0;
            })->count()
        ];
    }
}
