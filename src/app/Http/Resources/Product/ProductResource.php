<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $buyer = User::find($this->buyer_id);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'shoppingListId' => $this->shopping_list_id,
            'productImgUrl' => $this->image ? asset('storage/products/' . basename($this->image)) : null,
            'price' => $this->price,
            'buyer' => $buyer ? new UserResource($buyer) : null,
            'count' => $this->count
        ];
    }
}
