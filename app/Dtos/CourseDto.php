<?php
// app/DTOs/CourseDto.php

namespace App\DTOs;

final class CourseDto
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $slug = null,
        public readonly ?string $description = null,
        public readonly ?int $category_id = null,
        public readonly ?string $feature_video_path = null,
        public readonly ?string $feature_video_thumbnail = null,
        public readonly ?string $level = null,
        public readonly ?float $price = null,
        public readonly ?string $status = null,
        public readonly ?string $meta_title = null,
        public readonly ?string $meta_description = null,
        public readonly ?int $created_by = null,
        public readonly ?array $modules = null, // For nested creation
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            category_id: $data['category_id'] ?? null,
            feature_video_path: $data['feature_video_path'] ?? null,
            feature_video_thumbnail: $data['feature_video_thumbnail'] ?? null,
            level: $data['level'] ?? null,
            price: $data['price'] ?? null,
            status: $data['status'] ?? null,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            created_by: $data['created_by'] ?? null,
            modules: $data['modules'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'feature_video_path' => $this->feature_video_path,
            'feature_video_thumbnail' => $this->feature_video_thumbnail,
            'level' => $this->level,
            'price' => $this->price,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_by' => $this->created_by,
        ], static fn ($value) => !is_null($value));
    }
}
