<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class MessageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $authUserId = Auth::user()->id;
        $messages = $this->collection->toArray();
        foreach ($messages as $message) {
            if ((int) $message->author_id === (int) $authUserId) {
                $message->isAuthorIsAuth = true;
            } else {
                $message->isAuthorIsAuth = false;
                $message->authUserId = $authUserId;
            }
        }
        // return parent::toArray($request);
        return  $messages;


    }
}
