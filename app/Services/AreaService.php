<?php

namespace App\Services;

use App\Exceptions\AreaNotFoundException;
use App\Models\Area;
use App\Repositories\Contracts\AreaRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class AreaService
{
    public function __construct(
        protected AreaRepositoryInterface $areaRepository
    ) {}

    public function getAllAreas(Request $request): LengthAwarePaginator
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
            1 => 'city_id',
            2 => 'name',
            3 => 'created_at',
            4 => 'updated_at',
            5 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->areaRepository->all();

        if ($search && is_string($search)) {
            $query->where(function ($q) use ($search) {
                foreach ((new Area)->getFillable() as $column) {
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

        $query->when(! empty($fromDate) && ! empty($toDate) && ! empty($toDate), function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('date', [$fromDate.'00:00:00', $toDate.' 23:59:59']);
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

    public function getAreaById(int $id): ?Area
    {
        $area = $this->areaRepository->find($id);

        if (! $area) {
            throw new AreaNotFoundException;
        }

        return $area;
    }

    public function storeArea(ExpenseDto $expenseDto, array $data): Area
    {

        $expenseDto->created_by = auth()->id();

        return $this->areaRepository->create((array) $expenseDto);
    }

    public function storeArea(array $data): Area
    {
        $data['created_by'] = auth()->id();

        return $this->areaRepository->create($data);
    }

    public function updateArea(int $id, array $data): Area
    {

        $data['slug'] = Str::slug(reset($data) ?? '');

        $data['updated_by'] = auth()->id();

        return $this->areaRepository->update($id, $data);
    }

    public function deleteArea(int $id): bool
    {
        return $this->areaRepository->delete($id);
    }
}
