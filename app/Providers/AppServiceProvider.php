<?php

namespace App\Providers;

use App\Repositories\CourseRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CourseModuleRepository;
use App\Repositories\CourseModuleContentRepository;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\Contracts\CourseModuleRepositoryInterface;
use App\Repositories\Contracts\CourseModuleContentRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(CourseModuleRepositoryInterface::class, CourseModuleRepository::class);
        $this->app->bind(CourseModuleContentRepositoryInterface::class, CourseModuleContentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
