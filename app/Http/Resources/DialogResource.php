<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class DialogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $user = Auth::user();
        $dialogsUsers = $this->users;
        $dialogsMessages = $this->messages;
        $resultDialogsUsers = [];

        foreach ($dialogsUsers as $dialogsUser) {
            if ($dialogsUser->id !== $user->id) {
                array_push($resultDialogsUsers, new UserResource($dialogsUser));
            }
        }


        return [
            'dialogId' => $this->id,
            'dialogName' => $this->name,
            'isGroup' => $this->isGroup,
            'dialogsUsers' => $resultDialogsUsers,
            'dialogsMessages' => new MessageCollection($dialogsMessages)

        ];
    }
}