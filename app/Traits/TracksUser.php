<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

trait TracksUser
{
    /**
     * Boot the TracksUser trait.
     */
    public static function bootTracksUser(): void
    {
        static::creating(function ($model) {
            $userId = self::resolveAuthUserId();
            if (!$userId) return;

            if ($model->hasColumn('created_by') && empty($model->created_by)) {
                $model->created_by = $userId;
            }

            if ($model->hasColumn('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function ($model) {
            $userId = self::resolveAuthUserId();
            if ($userId && $model->hasColumn('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::deleting(function ($model) {
            $userId = self::resolveAuthUserId();
            if ($userId && $model->hasColumn('deleted_by')) {
                $model->deleted_by = $userId;
                $model->saveQuietly(); // prevent recursion
            }
        });

        static::restoring(function ($model) {
            if ($model->hasColumn('deleted_by')) {
                $model->deleted_by = null;
            }
        });
    }

    /**
     * Detect the currently authenticated user's ID by dynamically checking all guards.
     */
    protected static function resolveAuthUserId(): ?int
    {
        try {
            $guards = array_keys(Config::get('auth.guards', []));
            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    return Auth::guard($guard)->id();
                }
            }

            // fallback to default
            return Auth::id();
        } catch (\Throwable $e) {
            \Log::warning('TracksUser guard detection failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a given column exists on the model's table.
     */
    protected function hasColumn(string $column): bool
    {
        try {
            return Schema::hasColumn($this->getTable(), $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
