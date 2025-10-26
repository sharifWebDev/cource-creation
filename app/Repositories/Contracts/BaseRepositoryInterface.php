<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function get(Request $request): LengthAwarePaginator;

    public function getAll(): Collection;

    public function find(int $id): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): ?Model;

    public function delete(int $id): bool;
}
