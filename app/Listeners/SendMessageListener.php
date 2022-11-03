<?php

namespace App\Listeners;

use App\Events\SendMessage;
use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMessageListener
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
     * @param  \App\Events\SendMessage  $event
     * @return void
     */
    public function handle(SendMessage $event)
    {

        // return $event->message;
        return new PrivateChannel('new-message', $event->message->author_id);
    }
}
