<?php

namespace App\Dtos;

class CoursesDto implements \JsonSerializable
{
    private int $id;
    private string $title;
    private ?string $description;
    private int $category_id;
    private ?string $feature_video_path;
    private ?string $feature_video_thumbnail;
    private string $slug;
    private string $status;
    private int $created_by;
    private ?string $created_at;
    private ?string $updated_at;
    private ?string $deleted_at;

    public function __construct(array $data)
    {
        $this->setId($data['id'] ?? null);
        $this->setTitle($data['title'] ?? null);
        $this->setDescription($data['description'] ?? null);
        $this->setCategoryId($data['category_id'] ?? null);
        $this->setFeatureVideoPath($data['feature_video_path'] ?? null);
        $this->setFeatureVideoThumbnail($data['feature_video_thumbnail'] ?? null);
        $this->setSlug($data['slug'] ?? null);
        $this->setStatus($data['status'] ?? null);
        $this->setCreatedBy($data['created_by'] ?? null);
        $this->setCreatedAt($data['created_at'] ?? null);
        $this->setUpdatedAt($data['updated_at'] ?? null);
        $this->setDeletedAt($data['deleted_at'] ?? null);
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $value): void
    {
        $this->title = $value;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setDescription(?string $value): void
    {
        $this->description = $value;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setCategoryId(int $value): void
    {
        $this->category_id = $value;
    }

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function setFeatureVideoPath(?string $value): void
    {
        $this->feature_video_path = $value;
    }

    public function getFeatureVideoPath(): ?string
    {
        return $this->feature_video_path;
    }

    public function setFeatureVideoThumbnail(?string $value): void
    {
        $this->feature_video_thumbnail = $value;
    }

    public function getFeatureVideoThumbnail(): ?string
    {
        return $this->feature_video_thumbnail;
    }

    public function setSlug(string $value): void
    {
        $this->slug = $value;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setStatus(string $value): void
    {
        $this->status = $value;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setCreatedBy(int $value): void
    {
        $this->created_by = $value;
    }

    public function getCreatedBy(): int
    {
        return $this->created_by;
    }

    public function setCreatedAt(?string $value): void
    {
        $this->created_at = $value;
    }

    public function getCreatedAt(): ?string
    {
        if ($this->created_at instanceof \DateTimeInterface) {
            return $this->created_at -> format("Y-m-d H:i:s");
        }

        if (is_string($this->created_at)) {
            $timestamp = strtotime($this->created_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setUpdatedAt(?string $value): void
    {
        $this->updated_at = $value;
    }

    public function getUpdatedAt(): ?string
    {
        if ($this->updated_at instanceof \DateTimeInterface) {
            return $this->updated_at -> format("Y-m-d H:i:s");
        }

        if (is_string($this->updated_at)) {
            $timestamp = strtotime($this->updated_at);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }
        }

        return null;
    }

    public function setDeletedAt(?string $value): void
    {
        $this->deleted_at = $value;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deleted_at;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'category_id' => $this->getCategoryId(),
            'feature_video_path' => $this->getFeatureVideoPath(),
            'feature_video_thumbnail' => $this->getFeatureVideoThumbnail(),
            'slug' => $this->getSlug(),
            'status' => $this->getStatus(),
            'created_by' => $this->getCreatedBy(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'deleted_at' => $this->getDeletedAt(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}