<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Name' => $this->name,
            'Email' => $this->email,
            'Token' => $this->token,
            'Role' => $this->role,
            'Username' => $this->username,
        ];
    }
}
