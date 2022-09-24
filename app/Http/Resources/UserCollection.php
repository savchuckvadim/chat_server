<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

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
        $this->except('updated_at', 'name', 'surname');
        return [

            'totalCount' =>  $this->collection->count(),
            'users' => $this->collection,
            'resultCode' => 1,
        ];
    }
}
