<?php

namespace App\Notifications;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class NewMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = new MessageResource($message);
        return $this->message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toBroadcast($notifiable)
    {
        $authUser = Auth::user();
        $isAuthUserIsRecipient = false;
        $message = $this->message;
        $recipients = $message->recipients();

        foreach ($recipients as $recipient) {
            if ($authUser->id === $recipient->id) {
                $isAuthUserIsRecipient = true;
            }
        }
        if (!$isAuthUserIsRecipient) {
            return new BroadcastMessage([
                'message' => $message,

            ]);
            // $message = null;
            // return [new PrivateChannel('new-message', ['message' => $this->message])];
        } else {
            return false;
        }
    }
    /**
     * Получить содержимое сообщения.
     *
     * @param  mixed  $notifiable
     * @return Message
     */
    public function toVoice($notifiable)
    {
        $authUserId = Auth::user()->id;
        $isAuthUserIsRecipient = false;
        $recipients = $this->message->recipients();
        foreach ($recipients as $recipient) {
            if ($authUserId === $recipient->id) {
                $isAuthUserIsRecipient = true;
            }
        }
        if ($isAuthUserIsRecipient) {
            return $this->message;
        }
    }
}
