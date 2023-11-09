<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CaptionUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public string $caption;
    // public string $videoId;
    // public int $index;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public string $caption, 
        public string $videoId,
        public int $index)
    {
        // $this->caption = $caption;
        // $this->index = $index;
        // $this->videoId = $videoId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('caption');
    }
}
