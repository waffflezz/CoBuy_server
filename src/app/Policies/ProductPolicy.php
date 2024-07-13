<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProductPolicy
{
    public function groupMember(User $user, Group $group): bool
    {
        Log::debug('(PRODUCT) GROUP MEMBER: user_id: ' . $user->id . ' group_id: ' . $group->id . '| user in group members = ' . $group->users->contains($user));
        return $group->users->contains($user);
    }

    public function groupOwner(User $user, Group $group): bool
    {
        Log::debug('(PRODUCT) GROUP OWNER: user_id: ' . $user->id . ' group_id: ' . $group->id . '| user in group members = ' . $group->users->contains($user));
        return $user->id === $group->owner_id;
    }
}
