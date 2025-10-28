<?php

namespace App\Services;

use App\Models\CourseModule;
use App\DTOs\CourseModuleDto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions\CourseModuleNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\CourseModuleRepositoryInterface;

class CourseModuleService
{
    public function __construct(
        protected CourseModuleRepositoryInterface $courseModuleRepository
    ) {}

    public function getAllCourseModules(Request $request): LengthAwarePaginator
    {
        $length = $request->input('length', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sortColumnIndex = $request->input('order.0.column');
        $sortDirection = $request->input('order.0.dir', 'desc');

        $columns = [
            0 => 'id',
            1 => 'course_id',
            2 => 'title',
            3 => 'description',
            4 => 'order',
            5 => 'is_published',
            6 => 'created_at',
            7 => 'updated_at',
            8 => 'deleted_at'
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->courseModuleRepository->all();

        if ($search && is_string($search)) {
            $query->where(function ($q) use ($search) {
                foreach ((new CourseModule())->getFillable() as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%");
            });
        }

        $query->when(!empty($fromDate) && !empty($toDate) && !empty($toDate), function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('date', [$fromDate . '00:00:00', $toDate . ' 23:59:59']);
        });

        if ($status) {
            $query->where('status', $status);
        }

        $query->orderBy($sortColumn, $sortDirection);


        if (strtolower($length) == -1) {
            $all = $query->get()->count();
            return $query->paginate($all);
        }

        return $query->paginate($length);
    }

    public function getCourseModuleById(int $id): ?CourseModule
    {
        $courseModule = $this->courseModuleRepository->find($id);

        if (!$courseModule) {
            throw new CourseModuleNotFoundException();
        }
        return $courseModule;
    }

    public function storeCourseModule(ExpenseDto $expenseDto, array $data): CourseModule
    {


        $expenseDto->created_by = auth()->id();
        return $this->courseModuleRepository->create((array) $expenseDto);
    }

    public function storeCourseModule(array $data): CourseModule
    {
        $data['created_by'] = auth()->id();
        return $this->courseModuleRepository->create($data);
    }

    public function updateCourseModule(int $id, array $data): CourseModule
    {


        $data['slug'] = Str::slug(reset($data) ?? '');

        $data['updated_by'] = auth()->id();

        return $this->courseModuleRepository->update($id, $data);
    }

    public function deleteCourseModule(int $id): bool
    {
        return $this->courseModuleRepository->delete($id);
    }
}
