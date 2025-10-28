<?php
// app/Http/Resources/MediaFileResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaFileResource extends JsonResource
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
            'file_name' => $this->file_name,
            'original_name' => $this->original_name,
            'file_type' => $this->file_type,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->file_size_formatted,
            'extension' => $this->extension,
            'caption' => $this->caption,
            'alt_text' => $this->alt_text,
            'is_public' => $this->is_public,
            'url' => asset('storage/' . $this->file_path),
            'thumbnail_url' => $this->getThumbnailUrl(),
            'preview_url' => $this->getPreviewUrl(),
        ];
    }
}
