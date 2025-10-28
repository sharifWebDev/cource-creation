<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Course info
            'title' => 'required|string|max:255|unique:courses,title',
            'description' => 'required|string|min:10|max:2000',
            'category_id' => 'required|exists:course_categories,id',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|numeric|min:0',

            // ✅ Correct file field names
            'feature_video_path' => 'nullable|file|mimes:mp4,avi,mov,wmv|max:102400',
            'feature_video_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',

            // ✅ Modules
            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.description' => 'nullable|string|max:1000',
            'modules.*.order' => 'nullable|integer|min:0',

            // ✅ Contents
            'modules.*.contents' => 'required|array|min:1',
            'modules.*.contents.*.title' => 'required|string|max:255',
            'modules.*.contents.*.content_type_id' => 'required|exists:content_types,id',
            'modules.*.contents.*.order' => 'nullable|integer|min:1',
            'modules.*.contents.*.estimated_duration' => 'nullable|integer|min:0|max:480',

            // ✅ Dynamic content_data fields (optional by content type)
            'modules.*.contents.*.content_data.text' => 'nullable|string|max:10000',
            'modules.*.contents.*.content_data.video_url' => 'nullable|url|max:255',
            'modules.*.contents.*.content_data.video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,mp3|max:102400',
            'modules.*.contents.*.content_data.duration' => ['nullable','regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'modules.*.contents.*.content_data.image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10120',
            'modules.*.contents.*.content_data.caption' => 'nullable|string|max:255',
            'modules.*.contents.*.content_data.document_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:102400',
            'modules.*.contents.*.content_data.estimated_duration' => 'nullable|integer|min:0|max:480',
            'modules.*.contents.*.content_data.url' => 'nullable|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'modules.required' => 'At least one module is required.',
            'modules.*.contents.required' => 'Each module must have at least one content item.',
            'modules.*.contents.*.content_data.duration.regex' => 'Video length must be in HH:MM:SS format.',
            'modules.*.contents.*.content_data.video_url.url' => 'Please enter a valid video URL.',
        ];
    }

    public function attributes(): array
    {
        return [
            'modules.*.title' => 'module title',
            'modules.*.contents.*.title' => 'content title',
            'modules.*.contents.*.content_data.url' => 'video URL',
            'modules.*.contents.*.content_data.duration' => 'video duration',
        ];
    }
}
