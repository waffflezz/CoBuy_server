<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Group $group): bool
    {
        return Utils::isGroupOwner($user, $group);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): bool
    {
        return Utils::isGroupOwner($user, $group);
    }

    public function kick(User $user, Group $group): bool
    {
        return Utils::isGroupOwner($user, $group);
    }
}
