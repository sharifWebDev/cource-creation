<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseModuleContent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_module_id',
        'content_type_id',
        'title',
        'content_data',
        'order',
        'is_published',
        'estimated_duration'
    ];

    protected $casts = [
        'content_data' => 'array',
        'is_published' => 'boolean',
        'estimated_duration' => 'integer',
        'order' => 'integer',
    ];

    protected $appends = [
        'content_type_name',
        'content_type_slug',
        'preview_url',
        'validation_rules',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($content) {
            if (empty($content->order)) {
                $content->order = static::where('course_module_id', $content->course_module_id)->max('order') + 1;
            }
        });

        static::saving(function ($content) {
            $content->validateContentData();
        });
    }

    // Relationships
    public function courseModule(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function mediaFiles(): MorphMany
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }
    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('content_type_id', $typeId);
    }

    public function scopeWithMedia($query)
    {
        return $query->whereHas('contentType', function($q) {
            $q->where('has_media', true);
        });
    }

    public function scopeWithDuration($query, $minDuration = 0)
    {
        return $query->where('estimated_duration', '>=', $minDuration);
    }

    // Accessors & Mutators
    protected function contentTypeName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contentType->name ?? 'Unknown',
        );
    }

    protected function contentTypeSlug(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contentType->slug ?? 'unknown',
        );
    }

    protected function previewUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match($this->content_type_slug) {
                    'text' => null,
                    'image' => asset('storage/' . ($this->content_data['image_path'] ?? '')),
                    'video' => asset('storage/' . ($this->content_data['video_path'] ?? '')),
                    'document' => route('content.download', $this->id),
                    'link' => $this->content_data['url'] ?? '#',
                    'quiz' => route('quiz.preview', $this->id),
                    default => null,
                };
            },
        );
    }

    public function validateContentData2(): void
    {
        $contentType = $this->contentType;
        if (!$contentType || !$contentType->validation_rules) return;

        $validator = Validator::make(
            $this->content_data ?? [],
            $contentType->validation_rules
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function validateContentData(): void
    {
        $contentType = $this->contentType;
        if (!$contentType) return;

        $slug = $contentType->slug;
        $data = $this->content_data ?? [];

        if (!isset($data['content']) && isset($data['text'])) {
            $data['content'] = $data['text'];
        }

        $defaultRules = match ($slug) {
            'text' => ['text' => 'required|string'],
            'video' => ['video_file' => 'required|url', 'duration' => 'nullable|integer'],
            'document' => ['document_file' => 'required|string'],
            'image' => ['image_file' => 'required|string'],
            'link' => ['url' => 'required|url'],
            'quiz' => ['questions' => 'required|array|min:1'],
            default => [],
        };

        $rules = $contentType->validation_rules
            ? (is_string($contentType->validation_rules)
                ? json_decode($contentType->validation_rules, true)
                : $contentType->validation_rules)
            : $defaultRules;

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getContentDataValue(string $key, $default = null)
    {
        return data_get($this->content_data, $key, $default);
    }

    public function updateContentData(array $newData, bool $merge = true): bool
    {
        try {
            $currentData = $this->content_data ?? [];
            $updatedData = $merge ? array_merge($currentData, $newData) : $newData;

            $this->content_data = $updatedData;
            return $this->save();
        } catch (\Exception $e) {
            Log::error("Failed to update content data for content {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    public function getNextContent(): ?self
    {
        return static::where('course_module_id', $this->course_module_id)
                    ->where('order', '>', $this->order)
                    ->published()
                    ->ordered()
                    ->first();
    }

    public function getPreviousContent(): ?self
    {
        return static::where('course_module_id', $this->course_module_id)
                    ->where('order', '<', $this->order)
                    ->published()
                    ->orderBy('order', 'desc')
                    ->first();
    }

    public function isTextContent(): bool
    {
        return $this->content_type_slug === 'text';
    }

    public function isMediaContent(): bool
    {
        return in_array($this->content_type_slug, ['image', 'video', 'document']);
    }

    public function isInteractiveContent(): bool
    {
        return in_array($this->content_type_slug, ['quiz', 'link']);
    }

    public function getDownloadUrl(): ?string
    {
        if (!$this->isMediaContent()) return null;

        return match($this->content_type_slug) {
            'image', 'video', 'document' => route('content.download', $this->id),
            default => null,
        };
    }
}
