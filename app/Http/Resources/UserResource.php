<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
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
        $authUser = Auth::user();
        $user = User::find($this->id);
        $isContacted = false;
        $authUserDialogs = User::find($authUser->id)->getNotGroupDialogs();
        $userDialogs = $user->getNotGroupDialogs();
        foreach ($authUser->contacts as $contact) {
            if ($contact->contact_id == $this->id) {
                $isContacted = true;
            }
        }
        $dialogWidthAuthId = null;
        foreach ($authUserDialogs as $dialog) {
            if($user->isDialogExistInNotGroupDialogs($dialog->id)){
                $dialogWidthAuthId = $user->isDialogExistInNotGroupDialogs($dialog->id);
            }


        }

        return [
            '$user' => $user->name,
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'contacts' => $this->contacts,
            'isContacted' => $isContacted,
            'isSound' => $this->isSound,
            'isActive' => false,
            'update' => $this->updated_at,
            'dialogWidthAuthId' => $dialogWidthAuthId,
            '$authUserDialogs' => $authUserDialogs,
            '$userDialogs' => $userDialogs



        ];
    }
}
