<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'name' => $request->name,
            'email' => $request->email,
            'registered' => [
                'href' => 'api/v1/user/register',
                'method' => 'POST',
                'params' => 'email, password'
            ]
        ];     
    }
}
