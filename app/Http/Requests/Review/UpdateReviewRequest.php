<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'sometimes|integer|exists:users,id', // Allow optional update
            'book_id' => 'sometimes|integer|exists:books,id', // Allow optional update
            'rating' => 'sometimes|numeric|min:1|max:5', // Allow optional update
            'review' => 'sometimes|string', // Allow optional update
        ];
    }
}
