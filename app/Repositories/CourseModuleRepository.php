<?php
// app/Repositories/CourseModuleRepository.php

namespace App\Repositories;

use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\CourseModuleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CourseModuleRepository extends BaseRepository implements CourseModuleRepositoryInterface
{
    public function __construct(CourseModule $courseModule)
    {
        parent::__construct($courseModule);
    }

    protected function searchableColumns(): array
    {
        return ['title', 'description'];
    }

    protected function filterableColumns(): array
    {
        return ['course_id', 'is_published'];
    }

    protected function sortableColumns(): array
    {
        return ['id', 'title', 'order', 'created_at', 'updated_at'];
    }

    protected function withRelations(): array
    {
        return ['course', 'contents'];
    }

    public function filterQuery(Request $request): Builder
    {
        $q = $this->query()->with($this->withRelations());

        // Search
        if ($search = trim((string) $request->input('search'))) {
            $q->where(function (Builder $sub) use ($search) {
                foreach ($this->searchableColumns() as $col) {
                    $sub->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        // Course filter
        if ($courseId = $request->input('course_id')) {
            $q->where('course_id', $courseId);
        }

        // Published filter
        if ($request->has('is_published')) {
            $q->where('is_published', $request->boolean('is_published'));
        }

        // Sorting
        $index = $request->input('order.0.column');
        $dir   = $request->input('order.0.dir', 'asc');
        $columns = $this->sortableColumns();

        $sortColumn = is_numeric($index) && array_key_exists((int)$index, $columns)
            ? $columns[(int)$index]
            : 'order';

        $dir = strtolower($dir) === 'desc' ? 'desc' : 'asc';
        $q->orderBy($sortColumn, $dir);

        return $q;
    }

    public function paginateFiltered(Request $request): LengthAwarePaginator
    {
        $length = (int) $request->input('length', 10);
        $q = $this->filterQuery($request);

        if ((string)$request->input('length') === '-1') {
            $total = (clone $q)->count();
            return $q->paginate($total);
        }

        return $q->paginate($length);
    }

    public function export(Request $request): Collection
    {
        return $this->filterQuery($request)->get();
    }

    public function findByCourse(int $courseId): Collection
    {
        return $this->query()
            ->with(['contents.contentType'])
            ->where('course_id', $courseId)
            ->orderBy('order')
            ->get();
    }

    public function getPublishedModules(int $courseId): Collection
    {
        return $this->query()
            ->with(['publishedContents.contentType'])
            ->where('course_id', $courseId)
            ->where('is_published', true)
            ->orderBy('order')
            ->get();
    }

    public function getModuleWithContents(int $moduleId)
    {
        return $this->query()
            ->with(['course', 'contents.contentType', 'contents.mediaFiles'])
            ->find($moduleId);
    }

    public function reorderModules(int $courseId, array $order): bool
    {
        try {
            DB::transaction(function () use ($courseId, $order) {
                foreach ($order as $position => $moduleId) {
                    $this->update($moduleId, ['order' => $position]);
                }
            });
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to reorder modules for course {$courseId}: " . $e->getMessage());
            return false;
        }
    }

    public function getNextModule(int $courseId, int $currentOrder): ?object
    {
        return $this->query()
            ->where('course_id', $courseId)
            ->where('order', '>', $currentOrder)
            ->where('is_published', true)
            ->orderBy('order')
            ->first();
    }

    public function getPreviousModule(int $courseId, int $currentOrder): ?object
    {
        return $this->query()
            ->where('course_id', $courseId)
            ->where('order', '<', $currentOrder)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function getModuleStats(int $moduleId): array
    {
        $module = $this->find($moduleId);
        if (!$module) {
            return ['error' => 'Module not found'];
        }

        return [
            'total_contents' => $module->contents->count(),
            'published_contents' => $module->publishedContents->count(),
            'total_duration' => $module->contents->sum('estimated_duration'),
            'content_types_distribution' => $module->contents->groupBy('content_type_id')->map->count(),
        ];
    }
}
