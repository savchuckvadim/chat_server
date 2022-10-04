<?php

namespace App\Http\Resources;

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
        $authUserId = Auth::user()->id;
        $isAuthorIsAuth = false;
        if($this->author_id == $authUserId){
            $isAuthorIsAuth = true;
        }
        return [
            'id' => $this->id,
            'isAuthorIsAuth' => $isAuthorIsAuth,
            'dialogId' => $this->dialog_id,
            'body' => $this->body,
            'created' => $this->created_at,
            
        ];
    }
}
