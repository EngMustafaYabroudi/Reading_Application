<?php

namespace App\Http\Requests\ReadingGroup;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReadingGroupRequest extends FormRequest
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
            'name' => 'nullable|string|unique:reading_groups,name,' , // Validate name as optional, string, and unique in reading_groups table, excluding the current reading group's ID
            'description' => 'nullable|string', // Validate description as optional string
            'start_date' => 'nullable|date', // Validate start_date as optional and in date format
            'end_date' => 'nullable|date|after:start_date', // Validate end_date as optional, in date format, and after start_date
            'book_id' => 'nullable|integer|exists:books,id', // Validate book_id as optional integer and existing in books table
        ];
    }
}
