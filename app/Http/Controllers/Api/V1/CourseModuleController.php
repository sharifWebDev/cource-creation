<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Dtos\CourseModuleDto;
use App\Models\CourseModule;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\CourseModuleService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseModuleResource;
use App\Http\Requests\StoreCourseModuleRequest;
use App\Http\Requests\UpdateCourseModuleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\CourseService;

class CourseModuleController extends Controller
{

    public function __construct(
        protected CourseModuleService $courseModuleService,
        protected CourseService $courseService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $courseModules = $this->courseModuleService->getAllCourseModules($request);

            return success('Records retrieved successfully', CourseModuleResource::collection($courseModules));
        } catch (Exception $e) {
            info('Error retrieved CourseModule!', [$e]);
            return error('Course Modules retrieved failed!.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreCourseModuleRequest $request) : JsonResponse
    // {
    //    try {
    //         $dto = new ModlNameDto($request->validated());
    //         $courseModule = $this->courseModuleService->storeCourseModule($dto);

    //         return success('Records saved successfully', new CourseModuleResource($courseModule));

    //     } catch (Exception $e) {

    //         info('Course Modules data insert failed!', [$e]);
    //         return error('Course Modules insert failed!.');
    //     }
    // }

    public function store(StoreCourseModuleRequest $request): JsonResponse
    {
        try {

            $courseModule = $this->courseModuleService->storeCourseModule($request->validated());

            return success('Records saved successfully', new CourseModuleResource($courseModule));
        } catch (Exception $e) {

            info('Course Modules data insert failed!', [$e]);
            return error('Course Modules insert failed!.');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(CourseModule $course_module) : JsonResponse
    // {
    //     try {

    //         return success('Records retrieved successfully', new CourseModuleResource($course_module));

    //     } catch (\Exception $e) {
    //         info('Course Modules data showing failed!', [$e]);
    //         return error('Course Modules retrieved failed!.');
    //     }
    // }

    public function show(CourseModule $course_module): JsonResponse
    {
        try {

            $courseModule = $this->courseModuleService->getCourseModuleById($course_module->id);

            return success('Records retrieved successfully', new CourseModuleResource($courseModule));
        } catch (\Exception $e) {
            info('Course Modules data showing failed!', [$e]);
            return error('Course Modules retrieved failed!.');
        }
    }


    // public function edit(int $id): JsonResponse
    // {
    //     try {

    //     $course_module = $this->courseModuleService->getCourseModuleById($id);

    //     return success('Records retrieved successfully', new CourseModuleResource($course_module));

    //     } catch (\Exception $e) {
    //         info('Course Modules data showing failed!', [$e]);
    //         return error('Course Modules retrieval failed!');
    //     }
    // }


    public function edit(CourseModule $course_module): JsonResponse
    {
        try {

            return success('Records retrieved successfully', new CourseModuleResource($course_module));
        } catch (\Exception $e) {
            info('Course Modules data showing failed!', [$e]);
            return error('Course Modules retrieval failed!');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseModuleRequest $request, CourseModule $course_module): JsonResponse
    {
        try {

            $courseModule = $this->courseModuleService->updateCourseModule($course_module->id, $request->validated());

            return success('Records updated successfully', new CourseModuleResource($courseModule));
        } catch (\Exception $e) {
            info('Course Modules update failed!', [$e]);
            return error('Course Modules update failed!.');
        }
    }


    // public function update(UpdateCourseModuleRequest $request, int $id) : JsonResponse
    // {
    //     try {

    //         $courseModule = $this->courseModuleService->getCourseModuleById($id);

    //         $dto = new CourseModuleDto($request->validated());

    //         $this->courseModuleService->updateCourseModule($courseModule->id, $dto->toArray());
    //         return success('Records updated successfully', new CourseModuleResource($courseModule));

    //     } catch (\Exception $e) {
    //         info('Course Modules update failed!', [$e]);
    //         return error('Course Modules update failed!.');
    //     }
    // }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseModule $course_module): JsonResponse
    {
        try {

            if (! $course_module) {
                return response()->json(['message' => 'Course Modules not found!'], 404);
            }

            $courseModule = $this->courseModuleService->deleteCourseModule($course_module->id);
            return success('Records deleted successfully');
        } catch (\Exception $e) {
            info('Course Modules delete failed!', [$e]);
            return error('Course Modules delete failed!.');
        }
    }


    // public function destroy(int $id): JsonResponse
    // {
    //     try {

    //         $courseModule = $this->courseModuleService->getCourseModuleById($id);

    //         if (! $courseModule) {
    //             return response()->json(['message' => 'Course Modules not found!'], 404);
    //         }

    //         $this->courseModuleService->deleteCourseModule($CourseModule->id);

    //         return success('Records deleted successfully');

    //     } catch (\Exception $e) {
    //         info('Course Modules delete failed!', [$e]);
    //         return error('Course Modules delete failed!.');
    //     }
    // }
}
