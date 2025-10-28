<?php
// app/Http/Resources/CourseModuleContentResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleContentResource extends JsonResource
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
            'order' => $this->order,
            'is_published' => $this->is_published,
            'estimated_duration' => $this->estimated_duration,
            'content_data' => $this->content_data,

            // Relationships
            // 'module' => new CourseModuleResource($this->whenLoaded('courseModule')),
            // 'content_type' => new ContentTypeResource($this->whenLoaded('contentType')),
            // 'media_files' => MediaFileResource::collection($this->whenLoaded('mediaFiles')),

            // Computed attributes
            'content_type_name' => $this->content_type_name,
            'content_type_slug' => $this->content_type_slug,
            'preview_url' => $this->preview_url,
            'download_url' => $this->download_url,

            // Content type specific structured data
            'content_details' => $this->getContentDetails(),
        ];
    }

    /**
     * Get structured content details based on content type
     */
    private function getContentDetails(): array
    {
        $details = [
            'type' => $this->content_type_slug,
            'requires_completion' => $this->requiresCompletion(),
        ];

        switch ($this->content_type_slug) {
            case 'text':
                $details['text_content'] = [
                    'content' => $this->content_data['content'] ?? '',
                    'is_rich_text' => true,
                    'word_count' => str_word_count(strip_tags($this->content_data['content'] ?? '')),
                ];
                break;

            case 'image':
                $details['image_content'] = [
                    'image_url' => isset($this->content_data['image_path']) ? asset('storage/' . $this->content_data['image_path']) : null,
                    'thumbnail_url' => isset($this->content_data['thumbnail']) ? asset('storage/' . $this->content_data['thumbnail']) : null,
                    'caption' => $this->content_data['caption'] ?? null,
                    'alt_text' => $this->content_data['alt_text'] ?? null,
                    'dimensions' => $this->content_data['dimensions'] ?? null,
                ];
                break;

            case 'video':
                $details['video_content'] = [
                    'source_type' => $this->content_data['source_type'] ?? 'url',
                    'video_url' => $this->content_data['url'] ?? (isset($this->content_data['file_path']) ? asset('storage/' . $this->content_data['file_path']) : null),
                    'thumbnail_url' => isset($this->content_data['thumbnail']) ? asset('storage/' . $this->content_data['thumbnail']) : null,
                    'duration' => $this->content_data['duration'] ?? null,
                    'duration_seconds' => $this->content_data['duration_seconds'] ?? null,
                    'file_size' => $this->content_data['file_size'] ?? null,
                    'resolution' => $this->content_data['resolution'] ?? null,
                ];
                break;

            case 'document':
                $details['document_content'] = [
                    'file_url' => isset($this->content_data['file_path']) ? asset('storage/' . $this->content_data['file_path']) : null,
                    'file_name' => $this->content_data['file_name'] ?? null,
                    'file_size' => $this->content_data['file_size'] ?? null,
                    'file_size_formatted' => $this->formatFileSize($this->content_data['file_size'] ?? 0),
                    'file_type' => $this->content_data['file_type'] ?? null,
                    'page_count' => $this->content_data['page_count'] ?? null,
                ];
                break;

            case 'link':
                $details['link_content'] = [
                    'url' => $this->content_data['url'] ?? null,
                    'title' => $this->content_data['title'] ?? null,
                    'description' => $this->content_data['description'] ?? null,
                    'opens_in_new_tab' => $this->content_data['opens_in_new_tab'] ?? true,
                    'is_external' => $this->isExternalLink($this->content_data['url'] ?? ''),
                ];
                break;

            case 'quiz':
                $details['quiz_content'] = [
                    'questions' => $this->content_data['questions'] ?? [],
                    'passing_score' => $this->content_data['passing_score'] ?? 70,
                    'time_limit' => $this->content_data['time_limit'] ?? null,
                    'max_attempts' => $this->content_data['max_attempts'] ?? 1,
                    'shuffle_questions' => $this->content_data['shuffle_questions'] ?? false,
                    'show_results' => $this->content_data['show_results'] ?? true,
                    'total_questions' => count($this->content_data['questions'] ?? []),
                    'total_points' => $this->calculateTotalPoints($this->content_data['questions'] ?? []),
                ];
                break;

            default:
                $details['raw_data'] = $this->content_data;
                break;
        }

        return $details;
    }

    /**
     * Check if content requires completion tracking
     */
    private function requiresCompletion(): bool
    {
        return in_array($this->content_type_slug, ['video', 'quiz', 'document']);
    }

    /**
     * Format file size
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Check if link is external
     */
    private function isExternalLink(string $url): bool
    {
        $baseUrl = config('app.url');
        return !str_starts_with($url, $baseUrl) && !str_starts_with($url, '/');
    }

    /**
     * Calculate total points for quiz
     */
    private function calculateTotalPoints(array $questions): int
    {
        return array_reduce($questions, function ($carry, $question) {
            return $carry + ($question['points'] ?? 1);
        }, 0);
    }

}
