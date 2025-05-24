<?php

namespace App\Listeners;

use App\Events\PopulateChangeLog;
use App\Models\ChangeLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PopulateChangeLogListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PopulateChangeLog $event): void
    {
        $eventRequest = (array)$event;
        unset($eventRequest['socket']);
        ChangeLog::create($eventRequest);
    }
}
