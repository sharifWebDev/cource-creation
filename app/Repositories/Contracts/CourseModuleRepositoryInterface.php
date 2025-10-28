<?php
// app/Repositories/Contracts/CourseModuleRepositoryInterface.php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseModuleRepositoryInterface extends BaseRepositoryInterface
{
    public function filterQuery(Request $request): Builder;
    public function export(Request $request): Collection;

    // Domain-specific methods
    public function findByCourse(int $courseId): Collection;
    public function getPublishedModules(int $courseId): Collection;
    public function getModuleWithContents(int $moduleId);
    public function reorderModules(int $courseId, array $order): bool;
    public function getNextModule(int $courseId, int $currentOrder): ?object;
    public function getPreviousModule(int $courseId, int $currentOrder): ?object;

    // Analytics
    public function getModuleStats(int $moduleId): array;
}
