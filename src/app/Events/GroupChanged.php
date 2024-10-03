<?php

namespace App\Events;

use App\Http\Resources\Group\GroupResource;
use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Group $group;
    private EventType $eventType;

    /**
     * Create a new event instance.
     */
    public function __construct(Group $group, EventType $eventType)
    {
        $this->group = $group;
        $this->eventType = $eventType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('group-changed.' . $this->group->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'group-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->eventType->name,
            'data' => new GroupResource($this->group),
        ];
    }
}
