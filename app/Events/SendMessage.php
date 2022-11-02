<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SendMessage  implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct(Message $message)
    {
        $this->message = new MessageResource($message);
        return $this->message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // $authUser = Auth::user();
        // $isAuthUserIsRecipient = false;
        // foreach ($this->message->recipients as $recipient) {
        //     if ($authUser->id === $recipient->id) {
        //         $isAuthUserIsRecipient = true;
        //     }
        // }
        // if($isAuthUserIsRecipient){
            return [new PrivateChannel('new-message', ['message' => $this->message, 'recipients' => $this->message->author_id])];
        // }

    }

    public function broadcastAs()
    {
        return 'SendMessage';
    }
}
