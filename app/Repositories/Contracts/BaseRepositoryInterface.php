<?php
// app/Repositories/Contracts/BaseRepositoryInterface.php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /** ISP: read-only surface vs write ops are separate methods */

    // Read operations
    public function query(): Builder;
    public function all(): Collection;
    public function find(int $id): ?Model;
    public function findOrFail(int $id): Model;
    public function findBy(string $column, $value): ?Model;
    public function findWhere(array $conditions): Collection;
    public function first(): ?Model;
    public function count(): int;
    public function exists(): bool;

    /** ACID helper for updates inside transactions */
    public function findForUpdate(int $id): ?Model;

    /** generic pagination (can be overridden) */
    public function paginate(Request $request): LengthAwarePaginator;

    /** write operations */
    public function create(array $data): Model;
    public function update(int $id, array $data): ?Model;
    public function updateWhere(array $conditions, array $data): int;
    public function delete(int $id): bool;
    public function deleteWhere(array $conditions): int;
    public function restore(int $id): bool;
    public function forceDelete(int $id): bool;

    /** relationship operations */
    public function with(array $relations): self;
    public function load(Model $model, array $relations): Model;

    /** scopes */
    public function scope(callable $scope): self;

    /** bulk operations */
    public function createMany(array $data): Collection;
    public function updateMany(array $ids, array $data): bool;

    /** utility */
    public function getModel(): Model;
    public function getTable(): string;
}
