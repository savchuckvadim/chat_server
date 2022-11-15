<?php

namespace App\Http\Resources;

use App\Models\Dialog;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MessageResource extends JsonResource
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
        // $authUserId = Auth::user()->id;
        $isGroup = Dialog::find($this->dialog_id)->isGroup;
        // $isAuthorIsAuth = false;
        // if ($this->author_id == $authUserId) {
        //     $isAuthorIsAuth = true;
        // }
        return [
            'id' => $this->id,
            'isGroup' =>  $isGroup,
            'isForwarded' => $this->isForwarded,
            'isEditing' => $this->isEditing,
            'authorId' => $this->author_id,
            'isAuthorIsAuth' => $this->isAuthorIsAuth,
            'dialogId' => $this->dialog_id,
            'recipients' => $this->recipients(),
            'body' => $this->body,
            'created' => $this->created_at,

        ];
    }
}
