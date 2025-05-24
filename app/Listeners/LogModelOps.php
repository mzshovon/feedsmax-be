<?php

namespace App\Listeners;

use App\Events\ModelOps;
use App\Services\LoggerService;

class LogModelOps
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private LoggerService $log
    ){}

    /**
     * Handle the event.
     */
    public function handle(ModelOps $event): void
    {
        $this->log->init();
        $this->log->append($event->data);
        $this->log->close();
    }
}
