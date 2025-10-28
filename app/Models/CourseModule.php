<?php
// app/Models/CourseModule.php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    protected $appends = [
        'total_contents',
        'published_contents_count',
        'total_duration',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($module) {
            if (empty($module->order)) {
                $module->order = static::where('course_id', $module->course_id)->max('order') + 1;
            }
        });

        static::deleting(function ($module) {
            // Soft delete all contents when module is soft deleted
            if ($module->isForceDeleting()) {
                $module->contents()->withTrashed()->forceDelete();
            } else {
                $module->contents()->delete();
            }
        });

        static::restoring(function ($module) {
            $module->contents()->withTrashed()->restore();
        });
    }

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(CourseModuleContent::class)->orderBy('order');
    }

    public function publishedContents(): HasMany
    {
        return $this->hasMany(CourseModuleContent::class)
                    ->where('is_published', true)
                    ->orderBy('order');
    }

    public function mediaFiles(): \Illuminate\Database\Eloquent\Relations\MorphMany
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

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeWithContentsCount($query)
    {
        return $query->withCount(['contents', 'publishedContents']);
    }

    public function scopeWithDuration($query)
    {
        return $query->withSum('contents', 'estimated_duration');
    }

    // Accessors & Mutators
    protected function totalContents(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contents()->count(),
        );
    }

    protected function publishedContentsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contents()->where('is_published', true)->count(),
        );
    }

    protected function totalDuration(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->contents()->sum('estimated_duration'),
        );
    }


    // Business Logic Methods
    public function reorderContents(array $contentOrder): bool
    {
        try {
            \DB::transaction(function () use ($contentOrder) {
                foreach ($contentOrder as $order => $contentId) {
                    $this->contents()->where('id', $contentId)->update(['order' => $order]);
                }
            });
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to reorder contents for module {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    public function updateWithContents(array $data): bool
    {
        try {
            \DB::transaction(function () use ($data) {
                // Update module
                $this->update(collect($data)->except('contents')->toArray());

                // Handle contents
                if (isset($data['contents'])) {
                    $this->syncContents($data['contents']);
                }
            });
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to update module with contents {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    public function syncContents(array $contents): void
    {
        $existingContentIds = [];

        foreach ($contents as $contentData) {
            if (isset($contentData['id']) && !empty($contentData['id'])) {
                // Update existing content
                $content = $this->contents()->find($contentData['id']);
                if ($content) {
                    // Check if marked for removal
                    if (isset($contentData['_remove']) && $contentData['_remove']) {
                        $content->delete();
                        continue;
                    }

                    $content->update(collect($contentData)->except(['id', '_remove'])->toArray());
                    $existingContentIds[] = $content->id;
                }
            } else {
                // Create new content
                if (!(isset($contentData['_remove']) && $contentData['_remove'])) {
                    $content = $this->contents()->create(collect($contentData)->except('_remove')->toArray());
                    $existingContentIds[] = $content->id;
                }
            }
        }

        // Delete contents not in the current list (if not using soft delete, you might want to change this)
        $this->contents()->whereNotIn('id', $existingContentIds)->delete();
    }

    public function getNextModule(): ?self
    {
        return static::where('course_id', $this->course_id)
                    ->where('order', '>', $this->order)
                    ->published()
                    ->ordered()
                    ->first();
    }

    public function getPreviousModule(): ?self
    {
        return static::where('course_id', $this->course_id)
                    ->where('order', '<', $this->order)
                    ->published()
                    ->orderBy('order', 'desc')
                    ->first();
    }

    public function isFirstModule(): bool
    {
        return $this->order === static::where('course_id', $this->course_id)->min('order');
    }

    public function isLastModule(): bool
    {
        return $this->order === static::where('course_id', $this->course_id)->max('order');
    }

    // Statistical Methods
    public function getContentTypeDistribution(): Collection
    {
        return $this->contents()
                    ->with('contentType')
                    ->selectRaw('content_type_id, COUNT(*) as count')
                    ->groupBy('content_type_id')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item->contentType->name => $item->count];
                    });
    }

    public function getAverageContentDuration(): float
    {
        $duration = $this->contents()->avg('estimated_duration');
        return round($duration ?? 0, 2);
    }
}
