<?php

namespace App\Repositories;

use App\Models\Area;
use App\Repositories\Contracts\AreaRepositoryInterface;

class AreaRepository extends BaseRepository implements AreaRepositoryInterface
{
    public function __construct(Area $area)
    {
        parent::__construct($area);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function export($request)
    {
        return $this->model->query($request);
    }
}
