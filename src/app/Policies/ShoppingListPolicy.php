<?php

namespace App\Policies;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShoppingListPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, string $groupId): bool
    {
        return Utils::isUserInGroup($user, $groupId);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, string $groupId): bool
    {
        return Utils::isUserInGroup($user, $groupId);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $groupId): bool
    {
        return Utils::isUserInGroup($user, $groupId);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, string $groupId): bool
    {
        return Utils::isUserInGroup($user, $groupId);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, string $groupId): bool
    {
        return Utils::isUserInGroup($user, $groupId);
    }

}
