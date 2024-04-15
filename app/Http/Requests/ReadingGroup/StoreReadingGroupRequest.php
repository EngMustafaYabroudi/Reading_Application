<?php

namespace App\Http\Requests\ReadingGroup;

use Illuminate\Foundation\Http\FormRequest;

class StoreReadingGroupRequest extends FormRequest
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
            'name' => 'required|string|unique:reading_groups,name', // Validate name as required, string, and unique in reading_groups table
            'description' => 'nullable|string', // Validate description as optional string
            'start_date' => 'required|date', // Validate start_date as required and in date format
            'end_date' => 'required|date|after:start_date', // Validate end_date as required, in date format, and after start_date
            'book_id' => 'nullable|integer|exists:books,id', // Validate book_id as optional integer and existing in books table
        ];
    }
}
