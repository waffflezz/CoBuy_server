<?php

namespace App\Events;

use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupChanged implements ShouldBroadcast
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
            new PrivateChannel('group-changed.' . $this->group->id),
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
