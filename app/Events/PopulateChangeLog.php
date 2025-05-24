<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PopulateChangeLog
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $type;
    public string $table_name;
    public string $changed_by;
    public string|null $existing;
    public string|null $changes;
    /**
     * Create a new event instance.
     */
    public function __construct(
        string $type,
        string $table_name,
        string $changed_by,
        string|null $existing,
        string|null $changes,
    )
    {
        $this->type = $type;
        $this->table_name = $table_name;
        $this->changed_by = $changed_by;
        $this->existing = $existing;
        $this->changes = $changes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
