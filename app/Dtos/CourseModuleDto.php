<?php
// app/DTOs/CourseModuleDto.php

namespace App\DTOs;

final class CourseModuleDto
{
    public function __construct(
        public readonly ?int $course_id = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?int $order = null,
        public readonly ?bool $is_published = null,
        public readonly ?array $contents = null, // For nested creation
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            course_id: $data['course_id'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            order: $data['order'] ?? null,
            is_published: $data['is_published'] ?? true,
            contents: $data['contents'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'is_published' => $this->is_published,
        ], static fn ($v) => !is_null($v));
    }
}
