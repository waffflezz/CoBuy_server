<?php

namespace App\Events;

use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListChanged implements ShouldBroadcast
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
            new PrivateChannel('list-changed.' . $this->shoppingList->group_id),
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
