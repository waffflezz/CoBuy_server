<?php

namespace App\Events;

use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private ShoppingList $shoppingList;
    private EventType $eventType;

    /**
     * Create a new event instance.
     */
    public function __construct(ShoppingList $shoppingList, EventType $eventType)
    {
        $this->shoppingList = $shoppingList;
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
            new Channel('list-changed.' . $this->shoppingList->group_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'list-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->eventType->name,
            'data' => new ShoppingListResource($this->shoppingList),
        ];
    }
}
