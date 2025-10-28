<?php
// app/Repositories/Contracts/CourseRepositoryInterface.php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Course;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    public function filterQuery(Request $request): Builder;

    public function paginateFiltered(Request $request): LengthAwarePaginator;
}
