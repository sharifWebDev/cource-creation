<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|string',
            'feature_video_path' => 'required|string|max:255',
            'feature_video_thumbnail' => 'required|string|max:255',
            'status' => 'required|string|max:20',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'title.max' => 'The title field must not exceed 255 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'category_id.required' => 'The category_id field is required.',
            'category_id.numeric' => 'The category_id field must be a numeric.',
            'feature_video_path.required' => 'The feature_video_path field is required.',
            'feature_video_path.string' => 'The feature_video_path field must be a string.',
            'feature_video_path.max' => 'The feature_video_path field must not exceed 255 characters.',
            'feature_video_thumbnail.required' => 'The feature_video_thumbnail field is required.',
            'feature_video_thumbnail.string' => 'The feature_video_thumbnail field must be a string.',
            'feature_video_thumbnail.max' => 'The feature_video_thumbnail field must not exceed 255 characters.',
            'status.required' => 'The status field is required.',
            'status.string' => 'The status field must be a string.',
            'status.max' => 'The status field must not exceed 20 characters.',
        ];
    }
}
