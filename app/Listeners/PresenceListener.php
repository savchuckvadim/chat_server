<?php

namespace App\Listeners;

use App\Events\Presence;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PresenceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Presence  $event
     * @return void
     */
    public function handle(Presence $event)
    {
        return response([
            'event' => $event
        ]);
    }
}
