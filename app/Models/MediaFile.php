<?php

// app/Models/MediaFile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'file_name',
        'file_path',
        'original_name',
        'file_type',
        'mime_type',
        'file_size',
        'disk',
        'extension',
        'caption',
        'alt_text',
        'metadata',
        'uploaded_by',
        'mediable_type',
        'mediable_id',
        'is_public',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'is_public' => 'boolean',
    ];

    // Relationships
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function mediable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('file_type', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->where('file_type', 'document');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeLargeFiles($query, $sizeInMB = 10)
    {
        return $query->where('file_size', '>', $sizeInMB * 1024 * 1024);
    }

    // Helper methods
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        } else {
            return $bytes.' bytes';
        }
    }

    public function getFullPathAttribute()
    {
        return $this->disk === 'local'
            ? storage_path('app/'.$this->file_path)
            : public_path('storage/'.$this->file_path);
    }

    public function getUrlAttribute()
    {
        if ($this->disk === 'local') {
            return route('media.download', $this->id);
        }

        return asset('storage/'.$this->file_path);
    }

    public function getDimensionsAttribute()
    {
        if ($this->file_type !== 'image') {
            return null;
        }

        $metadata = $this->metadata;

        if (isset($metadata['width']) && isset($metadata['height'])) {
            return $metadata['width'].'x'.$metadata['height'];
        }

        return null;
    }

    public function getIsImageAttribute()
    {
        return $this->file_type === 'image';
    }

    public function getIsVideoAttribute()
    {
        return $this->file_type === 'video';
    }

    public function getIsDocumentAttribute()
    {
        return $this->file_type === 'document';
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->is_image) {
            return $this->url;
        }

        // Return default icons for non-image files
        if ($this->is_video) {
            return asset('images/video-thumbnail.png');
        }

        if ($this->is_document) {
            return asset('images/document-thumbnail.png');
        }

        return asset('images/file-thumbnail.png');
    }

    public static function getTotalStorageUsed($userId = null)
    {
        $query = $userId ? self::where('uploaded_by', $userId) : self::query();

        return $query->sum('file_size');
    }

    public static function getStorageUsedFormatted($userId = null)
    {
        $bytes = self::getTotalStorageUsed($userId);

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } else {
            return number_format($bytes / 1024, 2).' KB';
        }
    }
}
