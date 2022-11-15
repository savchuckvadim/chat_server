<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Notifications\NewMessage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function create($dialogId, $body, $isForwarded, $isEdited)
    {
        //dialogId, body, isForwarded, isEdited
        $author = Auth::user();
        $message = new Message();
        $message->dialog_id = $dialogId;
        $message->body = $body;
        $message->author_id = $author->id;
        $message->isForwarded = $isForwarded;
        $message->isEdited = $isEdited;

        $message->save();


        //DISPATCH EVENT
        SendMessage::dispatch($message);

        //SEND NOTIFICATION
        $recipients = $message->recipients();
        Notification::send($recipients, new NewMessage($message));

        $message->isAuthorIsAuth = true;
        return response([
            'resultCode' => 1,
            'createdMessage' => new MessageResource($message),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public static function edit($messageId, $body)
    {
        $message = Message::find($messageId);
        if ($message) {
            $message = $message->edit($body);


            //DISPATCH EVENT
            SendMessage::dispatch($message);

            // $message->save();
            $resultMessage = new MessageResource($message);
            $resultMessage->isAuthorIsAuth = true;
            return response([
                'resultCode' => 1,
                'editedMessage' => $resultMessage
            ]);
        } else {
            $message->edit($body);
            $resultMessage = new MessageResource($message);
            return response([
                'resultCode' => 0,
                'message' => 'message not found'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public static function destroy($messageId)
    {
       
        $message = Message::find($messageId);

        if ($message) {
            $message->delete();
            return response([
                'resultCode' => 1,
                'deletedMessageId' => $messageId
            ]);
        }
    }
}
