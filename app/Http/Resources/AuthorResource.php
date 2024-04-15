<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'biography' => $this->biography,
            'image_url' => $this->image,
        ];
        $books = $this->whenLoaded('books');
        if($books){
            $data['books'] = $books;
        }

        return $data;
    }
}
