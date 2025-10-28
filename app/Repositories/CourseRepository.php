<?php
// app/Repositories/CourseRepository.php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\CourseRepositoryInterface;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $course)
    {
        parent::__construct($course);
    }

    protected function searchableColumns(): array
    {
        return ['title', 'description', 'slug'];
    }

    protected function filterableColumns(): array
    {
        return ['category_id', 'status', 'level', 'created_by'];
    }

    protected function sortableColumns(): array
    {
        return ['id', 'title', 'category_id', 'status', 'created_at', 'updated_at'];
    }

    public function filterQuery(Request $request): Builder
    {
        $q = $this->query();

        // Search
        if ($search = trim((string) $request->input('search'))) {
            $q->where(function (Builder $sub) use ($search) {
                foreach ($this->searchableColumns() as $col) {
                    $sub->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        // Category filter
        if ($categoryId = $request->input('category_id')) {
            $q->where('category_id', $categoryId);
        }

        // Status filter
        if ($status = $request->input('status')) {
            $q->where('status', $status);
        }

        // Level filter
        if ($level = $request->input('level')) {
            $q->where('level', $level);
        }

        // Price filter
        if ($request->has('is_free')) {
            $q->where('price', $request->boolean('is_free') ? 0 : '>', 0);
        }

        // Instructor filter
        if ($instructorId = $request->input('instructor_id')) {
            $q->where('created_by', $instructorId);
        }

        // Date range
        $from = $request->input('from_date');
        $to   = $request->input('to_date');
        if (!empty($from) && !empty($to)) {
            $q->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"]);
        }

        // Sorting
        $index = $request->input('order.0.column');
        $dir   = $request->input('order.0.dir', 'desc');
        $columns = $this->sortableColumns();

        $sortColumn = is_numeric($index) && array_key_exists((int)$index, $columns)
            ? $columns[(int)$index]
            : 'created_at';

        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';
        $q->orderBy($sortColumn, $dir);

        return $q;
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $length = (int) $request->input('length', 10);

        $q = $this->query()
        ->with([
            'courseModules',
        ]);

        // dd(123);

        return $q->paginate($length);
    }

    public function findBySlug(string $slug): ?Course
    {
        return $this->query()
            ->with(['category', 'author', 'courseModules.publishedContents'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }
}
