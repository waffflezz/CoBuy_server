<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class ProductPolicy
{
    public function groupMember(User $user, Group $group): bool
    {
        return $group->users->contains($user);
    }

    public function groupOwner(User $user, Group $group): bool
    {
        return $user->id === $group->owner_id;
    }
}
