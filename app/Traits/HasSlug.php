<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait HasSlug
{
    /**
     * Boot method for the trait.
     */
    public static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->generateSlug();
        });

        static::updating(function ($model) {
            // If name/title changed, regenerate slug
            if ($model->isDirty($model->getSlugSourceField())) {
                $model->generateSlug();
            }
        });
    }

    /**
     * Generate a unique slug and assign it to the model.
     */
    protected function generateSlug(): void
    {
        $slugField = $this->getSlugField();
        $sourceField = $this->getSlugSourceField();
        $baseSlug = Str::slug($this->{$sourceField}, '-');

        $this->{$slugField} = DB::transaction(function () use ($baseSlug, $slugField) {
            $query = static::query()
                ->select($slugField)
                ->where($slugField, 'LIKE', $baseSlug . '%');

            if ($this->exists) {
                $query->where('id', '!=', $this->id);
            }

            $existingSlugs = $query->lockForUpdate()->pluck($slugField)->toArray();

            if (!in_array($baseSlug, $existingSlugs)) {
                return $baseSlug;
            }

            $max = 0;
            foreach ($existingSlugs as $existing) {
                if (preg_match('/^' . preg_quote($baseSlug, '/') . '-(\d+)$/', $existing, $matches)) {
                    $num = (int) $matches[1];
                    if ($num > $max) {
                        $max = $num;
                    }
                }
            }

            return $baseSlug . '-' . ($max + 1);
        });
    }

    /**
     * Define which model field will be used as the slug target.
     */
    protected function getSlugField(): string
    {
        return property_exists($this, 'slugField') ? $this->slugField : 'slug';
    }

    /**
     * Define which model field to use for slug generation.
     */
    protected function getSlugSourceField(): string
    {
        return property_exists($this, 'slugSourceField') ? $this->slugSourceField : 'name';
    }
}
