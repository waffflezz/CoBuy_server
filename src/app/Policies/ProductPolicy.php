<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, string $shoppingListId): bool
    {
        $shoppingList = ShoppingList::findOrFail($shoppingListId);
        return $this->isUserInGroup($user, $shoppingList->group_id);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return $this->isUserInGroup($user, $product->shoppingList->group_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $shoppingListId): bool
    {
        $shoppingList = ShoppingList::findOrFail($shoppingListId);
        return $this->isUserInGroup($user, $shoppingList->group_id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $this->isUserInGroup($user, $product->shoppingList->group_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $this->isUserInGroup($user, $product->shoppingList->group_id);
    }

    private function isUserInGroup(User $user, string $groupId): bool
    {
        return $user->groups->contains('id', $groupId);
    }
}
