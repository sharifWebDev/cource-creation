<?php
// app/Http/Controllers/CourseController.php

namespace App\Http\Controllers;

use Exception;
use App\Models\Course;
use App\DTOs\CourseDto;
use Illuminate\View\View;
use App\Models\ContentType;
use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\ContentTypeService;
use Illuminate\Http\RedirectResponse;
use App\Services\CourseCategoryService;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    public function __construct(
        protected CourseService $courseService,
        protected CourseCategoryService $courseCategoryService,
        protected ContentTypeService $contentTypeService
    ) {}

    /**
     * Display a listing of courses.
     */
    public function index(): View|RedirectResponse
    {
        try {
            return view('admin.courses.index');
        } catch (Exception $e) {
            info('Error showing courses', $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $categories = $this->courseCategoryService->getAll();
            $contentTypes = $this->contentTypeService->getAll();

            return view('admin.courses.create', compact('categories', 'contentTypes'));
        } catch (Exception $e) {
            info('Error loading create course form', $e->getMessage());
            return redirect()->route('admin.courses.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Store a newly created course.
     */
    // public function store(Request $request): JsonResponse
    public function store(StoreCourseRequest $request): JsonResponse
    {
        try {
            $dto = CourseDto::fromArray($request->validated());

            // dd($request->all());
            // dd($request->validated());

            $this->courseService->create($dto, $request);

            return success('Course created successfully.');
        } catch (Exception $e) {
            info($e->getMessage());
            return error('Course creation failed: ' . $e);
        }
    }

    /**
     * Display the specified course.
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            $course = $this->courseService->getById($id);

            return view('admin.courses.show', compact('course'));
        } catch (Exception $e) {
            info('Course not found!', $e->getMessage());
            return redirect()->route('admin.courses.index')->with('error', 'Course not found.');
        }
    }

    /**
     * Remove the specified course.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->courseService->delete($id);

            return success('Course deleted successfully.');
        } catch (Exception $e) {
            info($e->getMessage());
            return error('Course deletion failed: ' . $e->getMessage());
        }
    }



    /**
     * Process content data based on content type
     */
    private function processContentData(array $contentData): array
    {
        $processedData = $contentData['content_data'] ?? [];

        // Handle video content file upload
        if (isset($processedData['source_type']) && $processedData['source_type'] === 'upload') {
            if (isset($contentData['content_data']['file']) && $contentData['content_data']['file'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $contentData['content_data']['file'];
                $filePath = $file->store('courses/content-videos', 'public');
                $processedData['file_path'] = $filePath;
                $processedData['file_name'] = $file->getClientOriginalName();
                $processedData['file_size'] = $file->getSize();
            }
            // Remove the file object from the data
            unset($processedData['file']);
        }

        // Calculate duration in seconds from HH:MM:SS format
        if (isset($processedData['duration']) && is_string($processedData['duration'])) {
            $processedData['duration_seconds'] = $this->timeToSeconds($processedData['duration']);
        }

        return $processedData;
    }

    /**
     * Convert HH:MM:SS to seconds
     */
    private function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);
        if (count($parts) === 3) {
            return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        }
        return 0;
    }
}
