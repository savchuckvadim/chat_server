<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $authUser = Auth::user();
        $this->except('updated_at', 'name', 'surname');
        return [

            'totalCount' =>  $this->collection->count(),
            'users' => $this->collection->whereNotIn('id', $authUser->id)->toArray(),
            'resultCode' => 1,
        ];
    }
}
