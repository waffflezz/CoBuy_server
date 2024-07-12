<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class Utils
{
    public static function isUserInGroup(User $user, string $groupId): bool
    {
        return $user->groups->contains('id', $groupId);
    }

    public static function isGroupOwner(User $user, Group $group): bool
    {
        return $group->owner_id === $user->id;
    }
}
