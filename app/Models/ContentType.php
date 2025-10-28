<?php

// app/Models/ContentType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'schema',
        'validation_rules',
        'is_active',
        'sort_order',
        'has_media',
        'has_url',
        'has_text',
    ];

    protected $casts = [
        'schema' => 'array',
        'validation_rules' => 'array',
        'is_active' => 'boolean',
        'has_media' => 'boolean',
        'has_url' => 'boolean',
        'has_text' => 'boolean',
    ];

    // Relationships
    public function moduleContents()
    {
        return $this->hasMany(CourseModuleContent::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeWithMedia($query)
    {
        return $query->where('has_media', true);
    }

    public function scopeWithUrl($query)
    {
        return $query->where('has_url', true);
    }

    public function scopeWithText($query)
    {
        return $query->where('has_text', true);
    }
    public function scopeSelectable($query)
    {
        return $query->select('id', 'name');
    }

    // Helper methods
    public function getValidationRulesArray()
    {
        return $this->validation_rules ?? [];
    }

    public function getSchemaArray()
    {
        return $this->schema ?? [];
    }

    public function getIconHtmlAttribute()
    {
        if (strpos($this->icon, 'fa-') === 0) {
            return '<i class="'.$this->icon.'" style="color: '.$this->color.'"></i>';
        }

        return $this->icon;
    }

    public function getUsageCountAttribute()
    {
        return $this->moduleContents()->count();
    }

    public static function getBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }

    public static function getActiveTypes()
    {
        return static::active()->ordered()->get();
    }

    public static function getOptions()
    {
        return static::active()->ordered()->pluck('name', 'id');
    }
}
