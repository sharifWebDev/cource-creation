<?php

namespace App\Services;

use App\Models\CourseCategory;

class CourseCategoryService
{

    public function getAll()
    {
        return CourseCategory::active()->get();
    }

}
