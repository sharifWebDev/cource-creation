<?php
// app/Services/FileService.php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function storeCourseFeatureVideo(UploadedFile $file): string
    {
        return $file->store('courses/feature-videos', 'public');
    }

    public function storeCourseFeatureImage(UploadedFile $file): string
    {
        return $file->store('courses/feature-images', 'public');
    }

    public function storeContentVideo(UploadedFile $file): string
    {
        return $file->store('content/videos', 'public');
    }

    public function storeContentImage(UploadedFile $file): string
    {
        return $file->store('content/images', 'public');
    }

    public function storeContentDocument(UploadedFile $file): string
    {
        return $file->store('content/documents', 'public');
    }

    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    public function getFileUrl(string $path): ?string
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }
        return null;
    }

    public function getFileSize(string $path): int
    {
        return Storage::disk('public')->size($path);
    }

    public function getMimeType(string $path): string
    {
        return Storage::disk('public')->mimeType($path);
    }
}
