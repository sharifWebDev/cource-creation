<?php
// app/Repositories/Contracts/CourseModuleContentRepositoryInterface.php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseModuleContentRepositoryInterface extends BaseRepositoryInterface
{
    public function filterQuery(Request $request): Builder;
    public function paginateFiltered(Request $request): LengthAwarePaginator;
    public function findByModule(int $moduleId): Collection;
}
