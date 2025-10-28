<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'username' => $this->name ?? null,
            'phone' => $this->phone ?? null,
            'avatar_url' => $this->avatar_url ? asset('storage/' . $this->avatar_url) : asset('images/default-avatar.png'),
        ];
    }
}
