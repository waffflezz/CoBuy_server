<?php

namespace App\Services;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShoppingListService
{
    public function getShoppingList(User $user, string $shoppingListId)
    {
        $shoppingList = ShoppingList::whereIn('group_id', $user->groups->pluck('id'))->find($shoppingListId);
        if (!$shoppingList) {
            throw new ModelNotFoundException('ShoppingList by ID: ' . $shoppingListId . ' not found');
        }

        return $shoppingList;
    }
}
