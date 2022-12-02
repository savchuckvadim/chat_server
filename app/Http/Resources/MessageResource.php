<?php

namespace App\Http\Resources;

use App\Models\Dialog;
use App\Models\User;
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
      
        $dialog = Dialog::find($this->dialog_id);
        $isGroup = $dialog->isGroup;


        $author = User::find($this->author_id);
        $resultAuthor = new UserResource($author);

        return [
            'id' => $this->id,
            'isGroup' =>  $isGroup,
            'isForwarded' => $this->isForwarded,
            'isEdited' => $this->isEdited,
            'authorId' => $this->author_id,
            'author' => $resultAuthor,
            'isAuthorIsAuth' => $this->isAuthorIsAuth,
            'dialogId' => $this->dialog_id,
            'recipients' => $this->recipients(),
            'body' => $this->body,
            'created' => $this->created_at,

        ];
    }
}
