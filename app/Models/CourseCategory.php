<?php

// app/Models/CourseCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image_path',
        'meta_title',
        'meta_description',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(CourseCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CourseCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activeChildren()
    {
        return $this->hasMany(CourseCategory::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithChildren($query)
    {
        return $query->with(['children' => function ($q) {
            $q->active()->orderBy('sort_order');
        }]);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeSelectable($query)
    {
        return $query->select('id', 'name', 'parent_id');
    }

    // Helper methods
    public function getFullPathAttribute()
    {
        $path = [];
        $category = $this;

        while ($category) {
            $path[] = $category->name;
            $category = $category->parent;
        }

        return implode(' > ', array_reverse($path));
    }

    public function getCoursesCountAttribute()
    {
        return $this->courses()->count();
    }

    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    public function hasChildren()
    {
        return $this->children()->exists();
    }

    // For hierarchical dropdowns
    public static function getNestedList($excludeId = null)
    {
        $categories = self::with('children')
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        $list = [];

        foreach ($categories as $category) {
            if ($excludeId && $category->id == $excludeId) {
                continue;
            }
            $list[$category->id] = $category->name;

            foreach ($category->children as $child) {
                if ($excludeId && $child->id == $excludeId) {
                    continue;
                }
                $list[$child->id] = '-- '.$child->name;
            }
        }

        return $list;
    }
}
