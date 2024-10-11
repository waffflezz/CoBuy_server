<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'shoppingListId' => $this->shopping_list_id,
            'productImgUrl' => $this->image ? asset('storage/products/' . basename($this->image)) : null,
            'price' => $this->price,
            'userId' => $this->user_id
        ];
    }
}
