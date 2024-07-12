<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GroupService
{
    public function getGroupByUser(User $user, string $groupId)
    {
        $group = $user->groups()->find($groupId);
        if (!$group) {
            throw new ModelNotFoundException('Group not found by ID: ' . $groupId);
        }

        return $group;
    }
}
