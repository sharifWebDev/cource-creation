<?php
// app/Repositories/CourseModuleContentRepository.php

namespace App\Repositories;

use App\Models\CourseModuleContent;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\CourseModuleContentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CourseModuleContentRepository extends BaseRepository implements CourseModuleContentRepositoryInterface
{
    public function __construct(CourseModuleContent $content)
    {
        parent::__construct($content);
    }

    protected function searchableColumns(): array
    {
        return ['title'];
    }

    protected function filterableColumns(): array
    {
        return ['course_module_id', 'content_type_id', 'is_published'];
    }

    protected function sortableColumns(): array
    {
        return ['id', 'title', 'order', 'estimated_duration', 'created_at', 'updated_at'];
    }

    protected function withRelations(): array
    {
        return ['courseModule', 'contentType', 'mediaFiles'];
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

        // Module filter
        if ($moduleId = $request->input('course_module_id')) {
            $q->where('course_module_id', $moduleId);
        }

        // Content type filter
        if ($contentTypeId = $request->input('content_type_id')) {
            $q->where('content_type_id', $contentTypeId);
        }

        // Published filter
        if ($request->has('is_published')) {
            $q->where('is_published', $request->boolean('is_published'));
        }

        // Duration filter
        if ($minDuration = $request->input('min_duration')) {
            $q->where('estimated_duration', '>=', $minDuration);
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


    public function findByModule(int $moduleId): Collection
    {
        return $this->query()
            ->with(['contentType', 'mediaFiles'])
            ->where('course_module_id', $moduleId)
            ->orderBy('order')
            ->get();
    }

    public function getPublishedContents(int $moduleId): Collection
    {
        return $this->query()
            ->with(['contentType', 'mediaFiles'])
            ->where('course_module_id', $moduleId)
            ->where('is_published', true)
            ->orderBy('order')
            ->get();
    }

    public function getContentWithType(int $contentId)
    {
        return $this->query()
            ->with(['courseModule.course', 'contentType', 'mediaFiles'])
            ->find($contentId);
    }

    public function reorderContents(int $moduleId, array $order): bool
    {
        try {
            DB::transaction(function () use ($moduleId, $order) {
                foreach ($order as $position => $contentId) {
                    $this->update($contentId, [
                        'order' => $position,
                        'course_module_id' => $moduleId
                    ]);
                }
            });
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to reorder contents for module {$moduleId}: " . $e->getMessage());
            return false;
        }
    }

    public function getNextContent(int $moduleId, int $currentOrder): ?object
    {
        return $this->query()
            ->where('course_module_id', $moduleId)
            ->where('order', '>', $currentOrder)
            ->where('is_published', true)
            ->orderBy('order')
            ->first();
    }

    public function getPreviousContent(int $moduleId, int $currentOrder): ?object
    {
        return $this->query()
            ->where('course_module_id', $moduleId)
            ->where('order', '<', $currentOrder)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function getContentsByType(int $moduleId, int $contentTypeId): Collection
    {
        return $this->query()
            ->where('course_module_id', $moduleId)
            ->where('content_type_id', $contentTypeId)
            ->orderBy('order')
            ->get();
    }


    public function getContentStats(int $contentId): array
    {
        $content = $this->find($contentId);
        if (!$content) {
            return ['error' => 'Content not found'];
        }

        return [
            'content_type' => $content->contentType->name ?? 'Unknown',
            'duration' => $content->estimated_duration,
            'is_published' => $content->is_published,
            'media_files_count' => $content->mediaFiles->count(),
        ];
    }
}
