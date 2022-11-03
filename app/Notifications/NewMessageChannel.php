<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Notifications\Notification;

class NewMessageChannel
{
    /**
     * Отправить переданное уведомление.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, NewMessage $message)
    {
        // $message = $message->toVoice($notifiable);
        if($message){
            return [new PrivateChannel('new-message', ['message' => $message])];
        }

        // Отправка уведомления экземпляру `$notifiable` ...
    }
}
