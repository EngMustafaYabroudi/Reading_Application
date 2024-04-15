<?php

namespace App\Http\Resources;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // 'user' => new UserResource(User::find($this->user_id)), // Include user details
            // 'book' => new BookResource(Book::find($this->book_id)),
            'user_name' => User::find($this->user_id)->name, // Get user name
            'book_title' => Book::find($this->book_id)->title, // Get book title
            'rating' => $this->rating,
            'review' => $this->review,
        ];
    }
}
