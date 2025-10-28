<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization logic if needed
    }

    public function rules()
    {
        return [
            'course_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'required|string',
            'is_published' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'course_id.required' => 'The course_id field is required.',
            'course_id.numeric' => 'The course_id field must be a numeric.',
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'title.max' => 'The title field must not exceed 255 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'order.required' => 'The order field is required.',
            'order.numeric' => 'The order field must be a numeric.',
            'is_published.required' => 'The is_published field is required.',
            'is_published.boolean' => 'The is_published field must be a boolean.',
        ];
    }
}
