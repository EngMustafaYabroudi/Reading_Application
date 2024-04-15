<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReadingGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $book = $this->book; // Assuming you have direct access to the 'book' object

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
             'book_title' => $book ? $book->title : null, // Include book name if book exists
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
           
        ];
    }
}
