<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class GroupPolicy
{
    public function groupMember(User $user, Group $group): bool
    {
        return $group->users->contains($user);
    }

    public function groupOwner(User $user, Group $group): bool
    {
        return (int) $user->id === (int) $group->owner_id;
    }
}
