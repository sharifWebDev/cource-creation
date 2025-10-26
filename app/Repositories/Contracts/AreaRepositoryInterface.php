<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface AreaRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail($email);

    public function export(Request $request);
}
