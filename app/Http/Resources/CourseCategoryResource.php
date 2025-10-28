<?php
// app/Http/Resources/CourseCategoryResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,

            // // Relationships
            // 'parent' => new CourseCategoryResource($this->whenLoaded('parent')),
            // 'children' => CourseCategoryResource::collection($this->whenLoaded('children')),
            // 'courses' => CourseResource::collection($this->whenLoaded('courses')),

            // Computed attributes
            'full_path' => $this->full_path,
            'courses_count' => $this->when(isset($this->courses_count), $this->courses_count),
            'is_root' => $this->isRoot(),
            'has_children' => $this->hasChildren(),

            // Links
            'links' => [
                // 'self' => route('api.categories.show', $this->id),
                // 'courses' => route('api.categories.courses', $this->id),
            ],
        ];
    }
}
