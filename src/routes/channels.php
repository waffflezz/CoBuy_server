<?php

use App\Models\Group;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('group-changed.{groupId}', function ($user, string $groupId) {
    Log::debug('GROUP-CHANGED: ' . $user->groups->contains(Group::find($groupId)));
    return $user->groups->contains(Group::find($groupId));
});

Broadcast::channel('list-changed.{groupId}', function ($user, string $groupId) {
    return $user->groups->contains(Group::find($groupId));
});

Broadcast::channel('product-changed.{groupId}', function ($user, string $groupId) {
    return $user->groups->contains(Group::find($groupId));
});
