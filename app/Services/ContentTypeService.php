<?php

namespace App\Services;

use App\Models\ContentType;

class ContentTypeService
{

    public function getAll()
    {
        return ContentType::active()->get();
    }
}
