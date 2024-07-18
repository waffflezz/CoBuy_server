<?php

namespace App\Events;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductChanged implements ShouldBroadcast
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
            new PrivateChannel('product-changed.' . $this->product->shopping_list_id),
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
