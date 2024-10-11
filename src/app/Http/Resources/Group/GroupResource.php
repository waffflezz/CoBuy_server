<?php

namespace App\Http\Resources\Group;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avaUrl' => $this->image ? asset('storage/groups/' . basename($this->image)) : null,
            'inviteLink' => $this->invite_link,
            'owner' => $this->owner_id,
            'membersCount' => $this->users->count(),
            'listsCount' => $this->shoppingLists->count(),
            'members' => UserResource::collection($this->users)
        ];
    }
}
