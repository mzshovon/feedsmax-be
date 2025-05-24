<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelOps
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array|string $data;
    /**
     * Create a new event instance.
     */
    /**
     * @param array|string $data
     */
    public function __construct(array|string $data)
    {
        $this->data = $data;
    }

}
