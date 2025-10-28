<?php
// app/Http/Resources/CourseResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
            'price' => $this->price,
            'status' => $this->status,
            'feature_video_url' => $this->feature_video_path ? asset('storage/' . $this->feature_video_path) : null,
            'feature_image_url' => $this->feature_image_path ? asset('storage/' . $this->feature_image_path) : null,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_by' => $this->created_by,
            'published_at' => $this->published_at?->toISOString(),

            // Relationships
            'category' => new CourseCategoryResource($this->whenLoaded('category')),
            'author' => new UserResource($this->whenLoaded('author')),
            'category_name' => $this->whenLoaded('category', function () {
                return $this->category ? $this->category->name : null;
            }),
            'authors_name' => $this->whenLoaded('author', function () {
                return $this->author ? $this->author->name : null;
            }),

            // Nested Modules with their Contents
            'modules' => $this->whenLoaded('courseModules', function () {
                return CourseModuleResource::collection($this->courseModules->load('contents.contentType'));
            }),

            // Alternative: Load modules with published contents only
            'published_modules' => $this->whenLoaded('publishedCourseModules', function () {
                return CourseModuleResource::collection(
                    $this->publishedCourseModules->load(['publishedContents.contentType'])
                );
            }),

            // Computed attributes
            'total_modules' => $this->when(isset($this->course_modules_count), $this->course_modules_count),
            'published_modules_count' => $this->when(isset($this->published_modules_count), $this->published_modules_count),
            'total_contents' => $this->when(isset($this->total_contents), $this->total_contents),
            'enrollments_count' => $this->when(isset($this->enrollments_count), $this->enrollments_count),
            'is_free' => $this->price == 0,
            'is_published' => $this->status === 'published',
            'is_draft' => $this->status === 'draft',
            'is_archived' => $this->status === 'archived',

            // Progress tracking (if user context available)
            'user_progress' => $this->when(auth()->check(), function () {
                return [
                    'progress_percentage' => $this->getUserProgressPercentage(auth()->id()),
                    'completed_modules' => $this->getUserCompletedModulesCount(auth()->id()),
                    'completed_contents' => $this->getUserCompletedContentsCount(auth()->id()),
                    'total_time_spent' => $this->getUserTotalTimeSpent(auth()->id()),
                    'last_accessed_at' => $this->getUserLastAccessedAt(auth()->id()),
                ];
            }),

            // Content type distribution
            'content_type_distribution' => $this->whenLoaded('courseModules', function () {
                return $this->getContentTypeDistribution();
            }),

            // Links
            'links' => [
                'self' => route('api.courses.show', $this->id),
                'web' => route('admin.courses.show', $this->slug),
                'edit' => route('admin.courses.edit', $this->id),
            ],
        ];
    }

    /**
     * Get user progress percentage
     */
    private function getUserProgressPercentage(int $userId): float
    {
        // Implementation depends on your progress tracking system
        $totalContents = $this->getTotalContentsCount();
        if ($totalContents === 0) return 0;

        $completedContents = $this->getUserCompletedContentsCount($userId);
        return round(($completedContents / $totalContents) * 100, 2);
    }

    /**
     * Get total contents count
     */
    private function getTotalContentsCount(): int
    {
        if ($this->relationLoaded('courseModules') && $this->courseModules) {
            return $this->courseModules->sum(function ($module) {
                return $module->contents->count();
            });
        }

        return 0;
    }

    /**
     * Get user completed modules count
     */
    private function getUserCompletedModulesCount(int $userId): int
    {
        // Implementation depends on your progress tracking system
        // This is a placeholder - implement based on your business logic
        return 0;
    }

    /**
     * Get user completed contents count
     */
    private function getUserCompletedContentsCount(int $userId): int
    {
        // Implementation depends on your progress tracking system
        // This is a placeholder - implement based on your business logic
        return 0;
    }

    /**
     * Get user total time spent
     */
    private function getUserTotalTimeSpent(int $userId): int
    {
        // Implementation depends on your progress tracking system
        // This is a placeholder - implement based on your business logic
        return 0;
    }

    /**
     * Get user last accessed at
     */
    private function getUserLastAccessedAt(int $userId): ?string
    {
        // Implementation depends on your progress tracking system
        // This is a placeholder - implement based on your business logic
        return null;
    }

    /**
     * Get content type distribution
     */
    private function getContentTypeDistribution(): array
    {
        $distribution = [];

        if ($this->relationLoaded('courseModules') && $this->courseModules) {
            foreach ($this->courseModules as $module) {
                if ($module->relationLoaded('contents') && $module->contents) {
                    foreach ($module->contents as $content) {
                        $contentTypeName = $content->content_type_name;
                        $distribution[$contentTypeName] = ($distribution[$contentTypeName] ?? 0) + 1;
                    }
                }
            }
        }

        return $distribution;
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'api_version' => 'v1',
                'copyright' => config('app.name'),
                'authors' => [
                    'Your Company'
                ],
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
