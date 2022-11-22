<?php

namespace App\Http\Resources;

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
        $isContacted = false;

        foreach ($authUser->contacts as $contact) {
            if ($contact->contact_id == $this->id) {
                $isContacted = true;
            }
        }

        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'contacts' => $this->contacts,
            'isContacted' => $isContacted,
            'isSound' => $this->isSound,
            'isActive' => $this->isActive,
            'update' => $this->updated_at



        ];
    }
}
