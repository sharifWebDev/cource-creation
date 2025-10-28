<?php

namespace App\Exceptions;

use Exception;

class CourseNotFoundException extends Exception
{
    protected $message = 'Course not found.';
    protected $code = 404;
}
