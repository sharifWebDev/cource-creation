<?php
// app/Http/Resources/CourseModuleResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Relationships
            'course' => new CourseResource($this->whenLoaded('course')),

            // Nested Contents
            'contents' => $this->whenLoaded('contents', function () {
                return CourseModuleContentResource::collection($this->contents->load('contentType'));
            }),

            // Alternative: Only published contents
            'published_contents' => $this->whenLoaded('publishedContents', function () {
                return CourseModuleContentResource::collection($this->publishedContents->load('contentType'));
            }),

            // Computed attributes
            'total_contents' => $this->when(isset($this->contents_count), $this->contents_count, function () {
                return $this->relationLoaded('contents') ? $this->contents->count() : null;
            }),
            'published_contents_count' => $this->when(isset($this->published_contents_count), $this->published_contents_count),

            // Content type distribution for this module
            'content_type_distribution' => $this->whenLoaded('contents', function () {
                return $this->getContentTypeDistribution();
            }),
            // Links
            'links' => [
                // 'self' => route('api.modules.show', $this->id),
                // 'contents' => route('api.modules.contents.index', $this->id),
            ],
        ];
    }


}
