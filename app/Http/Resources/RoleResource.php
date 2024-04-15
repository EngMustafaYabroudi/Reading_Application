<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $userData = User::whereHas('roles', function ($query) {
            $query->where('role_id', $this->id);
        })->get(); // Fetch all users with the specified role

        if ($userData->isEmpty()) {
            // Handle the case where no users are found with the specified role
            return [
                'id' => $this->id,
                'name' => $this->name,
                'permissions' => $this->permissions->pluck('name'), 
                'created_at' => $this->created_at->format('Y-m-d'),
                'updated_at' => $this->updated_at->format('Y-m-d'),
                'users' => [], // Return an empty array for clarity
            ];
        } else {
            // Extract all usernames
            $usernames = $userData->pluck('name');

            return [
                'id' => $this->id,
                'name' => $this->name,
                // 'created_at' => $this->created_at->format('Y-m-d'),
                // 'updated_at' => $this->updated_at->format('Y-m-d'),
                'permissions' => $this->permissions->pluck('name'), 
                'users' => $usernames, // Return an array of all usernames
            ];
        }
    }
}
