<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDialog;
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


    public $isSound;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct(Message $message, $recipientId)
    {
        $this->recipientId = $recipientId;
        $recipient = User::find($recipientId);
        $this->message = $message;
        $this->message->isAuthorIsAuth = false;
        $dialog = $this->message->dialog;

        $dialogId = $dialog->id;
        $relation = UserDialog::where('dialog_id', $dialogId)->where('user_id', $recipientId)->first();

        $this->isSound = false;
        if ($recipient->isSound && $relation->isSound) {
            $this->isSound = true;
        }

        $this->message = new MessageResource($this->message);
        return [
            'message' => $this->message,
            'isSound' => $this->isSound

        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $resultChannels = [];
        $recipients = $this->message->recipients();

        //if recipients - is sound
        // and dialog:
        // ->where('dialog_id', message-dialogId)
        // ->where('user_id', $recipient.id)

        foreach ($recipients as $recipient) {

            $relation = UserDialog::where('dialog_id', $this->message->dialogId)
                ->where('user_id', $recipient->id)->first();
            $relationIsSound = false;
            $recipientIsSound = false;


            if ($relation) {
                $relationIsSound =  $relation->isSound;
                $recipientIsSound = $recipient->isSound;


                if ($relationIsSound && $recipientIsSound) {
                    $this->message->isSound = true;
                    $this->message->save();
                }
            }

            $resultMessage = new MessageResource($this->message->id);
            $resultMessage->isSound = true;
            $channel = new PrivateChannel(
                'new-message.' . $recipient->id,
                [
                    'message' => $resultMessage,


                ]
            );
            array_push($resultChannels, $channel);
        }

        return $resultChannels;
    }

    public function broadcastAs()
    {
        return 'SendMessage';
    }
}
