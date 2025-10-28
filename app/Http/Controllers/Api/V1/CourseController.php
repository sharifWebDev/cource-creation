<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CourseService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;

class CourseController extends Controller
{

    public function __construct(
    protected CourseService $courseService
    )
    {}

    public function index(Request $request) : JsonResponse
    {
        try {
            $courses = $this->courseService->get($request);

            return success('Records retrieved successfully', $courses);
        } catch (Exception $e) {
            info('Error retrieved Course!', [$e]);
            return error('Courses retrieved failed!.'. $e);
        }
    }

    public function show(int $id) : JsonResponse
    {
        try {
            $course = $this->courseService->getById($id);

            return success('Records retrieved successfully', new CourseResource($course));
        } catch (Exception $e) {
            info('Courses data showing failed!', [$e]);
            return error('Courses retrieved failed!.');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->courseService->delete($id);

            return success('Records deleted successfully');
        } catch (Exception $e) {
            info('Courses delete failed!', [$e]);
            return error('Courses delete failed!.');
        }
    }
}
