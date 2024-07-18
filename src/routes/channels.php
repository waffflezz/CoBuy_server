<?php

use App\Models\Group;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('group-changed.{groupId}', function ($user, string $groupId) {
    return $user->groups->contains(Group::find($groupId));
});

Broadcast::channel('list-changed.{groupId}', function ($user, string $groupId) {
    return $user->groups->contains(Group::find($groupId));
});

Broadcast::channel('product-changed.{groupId}', function ($user, string $groupId) {
    return $user->groups->contains(Group::find($groupId));
});
