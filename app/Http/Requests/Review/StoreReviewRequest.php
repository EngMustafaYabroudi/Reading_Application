<?php

namespace App\Http\Requests\Review;

use App\Models\Review;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
        Validator::extend('unique_review', function ($attribute, $value, $parameters, $validator) {
            $userId = request()->input('user_id'); // Get user ID from request
            $bookId = request()->input('book_id'); // Get book ID from request
        
            return !Review::where('user_id', $userId)
                         ->where('book_id', $bookId)
                         ->exists();
        }, 'You can only create one review for this book.');
        return [
            'user_id' => 'required|integer|exists:users,id', // Ensure user exists
            'book_id' => 'required|integer|exists:books,id', // Ensure book exists
            'rating' => 'required|numeric|min:1|max:5', // Valid rating range
            'review' => 'required|string', // Review content validation (optional)
            'unique_review'=>'unique_review'
            // 'user_id_book_id' => 'unique:reviews,user_id,book_id', // Custom validation rule name
        ];
    }
}
