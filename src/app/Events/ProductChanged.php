<?php

namespace App\Events;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Product $product;
    private EventType $eventType;

    /**
     * Create a new event instance.
     */
    public function __construct(Product $product, EventType $eventType)
    {
        $this->product = $product;
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
            new Channel('product-changed.' . $this->product->shopping_list_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'product-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->eventType->name,
            'data' => new ProductResource($this->product),
        ];
    }
}
