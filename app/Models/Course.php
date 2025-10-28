<?php

// app/Models/Course.php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes, HasSlug, TracksUser;

    protected $slugField = 'slug';         // optional (default: slug)
    protected $slugSourceField = 'title';  // optional (default: name)

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'feature_video_path',
        'feature_video_thumbnail',
        'slug',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function courseModules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publishedCourseModules()
    {
        return $this->hasMany(CourseModule::class)->where('is_published', true)->orderBy('order');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Helper methods
    public function getTotalModulesAttribute()
    {
        return $this->courseModules()->count();
    }

    public function getPublishedModulesAttribute()
    {
        return $this->courseModules()->where('is_published', true)->count();
    }

    public function getTotalDurationAttribute()
    {
        return $this->courseModules()->with('contents')->get()->sum(function ($module) {
            return $module->contents->sum('estimated_duration');
        });
    }

    public function courses()
    {
        return $this->hasMany(\App\Models\CourseModule::class, 'course_id');
    }
}
