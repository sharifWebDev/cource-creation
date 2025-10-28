<?php
// app/DTOs/CourseModuleContentDto.php

namespace App\DTOs;

final class CourseModuleContentDto
{
    public function __construct(
        public readonly ?int $course_module_id = null,
        public readonly ?int $content_type_id = null,
        public readonly ?string $title = null,
        public readonly ?array $content_data = null,
        public readonly ?int $order = null,
        public readonly ?bool $is_published = null,
        public readonly ?int $estimated_duration = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            course_module_id: $data['course_module_id'] ?? null,
            content_type_id: $data['content_type_id'] ?? null,
            title: $data['title'] ?? null,
            content_data: $data['content_data'] ?? null,
            order: $data['order'] ?? null,
            is_published: $data['is_published'] ?? true,
            estimated_duration: $data['estimated_duration'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'course_module_id' => $this->course_module_id,
            'content_type_id' => $this->content_type_id,
            'title' => $this->title,
            'content_data' => $this->content_data,
            'order' => $this->order,
            'is_published' => $this->is_published,
            'estimated_duration' => $this->estimated_duration,
        ], static fn ($v) => !is_null($v));
    }
}
