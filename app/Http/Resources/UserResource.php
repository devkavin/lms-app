<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $response = match ($this->getRoleNames()->first()) {
            'admin' => AdminResource::make($this)->resolve(), // resolve() method is used to get the data from the resource and not the resource itself
            'instructor' => InstructorResource::make($this)->resolve(),
            'student' => StudentResource::make($this)->resolve(),
            default => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->getRoleNames(),
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            ],
        };

        return  $response;
    }
}
