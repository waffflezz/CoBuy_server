<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('group-changed.{group_id}', function ($user, int $groupId) {
    return $user->groups->contains('id', $groupId);
});

Broadcast::channel('list-changed.{group_id}', function ($user, int $group_id) {
    return $user->groups->contains('id', $group_id);
});

Broadcast::channel('product-changed.{group_id}', function ($user, int $group_id) {
    return $user->groups->contains('id', $group_id);
});
