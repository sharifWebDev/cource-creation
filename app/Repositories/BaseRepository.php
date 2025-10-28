<?php
// app/Repositories/BaseRepository.php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;
    protected Builder $query;
    protected array $withRelations = [];

    /** SRP: one responsibility – persistence of a specific model */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->query = $model->newQuery();
    }

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(): Collection
    {
        return $this->buildQuery()->get();
    }

    public function find(int $id): ?Model
    {
        return $this->buildQuery()->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->buildQuery()->findOrFail($id);
    }

    public function findBy(string $column, $value): ?Model
    {
        return $this->buildQuery()->where($column, $value)->first();
    }

    public function findWhere(array $conditions): Collection
    {
        return $this->buildQuery()->where($conditions)->get();
    }

    public function first(): ?Model
    {
        return $this->buildQuery()->first();
    }

    public function count(): int
    {
        return $this->buildQuery()->count();
    }

    public function exists(): bool
    {
        return $this->buildQuery()->exists();
    }

    public function findForUpdate(int $id): ?Model
    {
        return $this->buildQuery()->lockForUpdate()->find($id);
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        $length = (int) $request->input('length', 10);
        return $this->buildQuery()->paginate($length);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $record = $this->find($id);
        return $record ? tap($record)->update($data) : null;
    }

    public function updateWhere(array $conditions, array $data): int
    {
        return $this->model->where($conditions)->update($data);
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        return $record ? (bool) $record->delete() : false;
    }

    public function deleteWhere(array $conditions): int
    {
        return $this->model->where($conditions)->delete();
    }

    public function restore(int $id): bool
    {
        $record = $this->model->withTrashed()->find($id);
        return $record ? (bool) $record->restore() : false;
    }

    public function forceDelete(int $id): bool
    {
        $record = $this->model->withTrashed()->find($id);
        return $record ? (bool) $record->forceDelete() : false;
    }

    public function with(array $relations): self
    {
        $this->withRelations = array_merge($this->withRelations, $relations);
        return $this;
    }

    public function load(Model $model, array $relations): Model
    {
        return $model->load($relations);
    }

    public function scope(callable $scope): self
    {
        $this->query = $scope($this->query);
        return $this;
    }

    public function createMany(array $data): Collection
    {
        $models = new Collection();

        foreach ($data as $item) {
            $models->push($this->create($item));
        }

        return $models;
    }

    public function updateMany(array $ids, array $data): bool
    {
        return $this->model->whereIn('id', $ids)->update($data) > 0;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getTable(): string
    {
        return $this->model->getTable();
    }

    /** Reset the query builder */
    protected function resetQuery(): void
    {
        $this->query = $this->model->newQuery();
        $this->withRelations = [];
    }

    /** Build the final query with relations */
    protected function buildQuery(): Builder
    {
        $query = $this->query->with($this->withRelations);
        $this->resetQuery();
        return $query;
    }

    /** Get searchable columns for filtering (to be overridden by child classes) */
    protected function searchableColumns(): array
    {
        return [];
    }

    /** Get sortable columns (to be overridden by child classes) */
    protected function sortableColumns(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    /** Get default relations to load (to be overridden by child classes) */
    protected function withRelations(): array
    {
        return ['category', 'author', 'courseModules'];
    }

}
