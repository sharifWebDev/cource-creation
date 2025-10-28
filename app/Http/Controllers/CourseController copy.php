<?php


namespace App\Http\Controllers;

use Exception;
use App\Models\Course;
use Illuminate\View\View;
use App\Models\ContentType;
use Illuminate\Support\Str;
use App\Models\CourseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreCourseRequest;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|RedirectResponse
    {
        try {
            $courses = Course::with(['category', 'author'])
                ->latest()
                ->paginate(10);

            return view('admin.courses.index', compact('courses'));
        } catch (Exception $e) {

            info('Error showing Courses!', [$e]);

            return redirect()->back()->with('error', 'Courses showing failed!.');
        }
    }

    public function create(): View|RedirectResponse
    {
        try {
            $categories = CourseCategory::all();
            $contentTypes = ContentType::all();

            return view('admin.courses.create', compact('categories', 'contentTypes'));
        } catch (Exception $e) {
            info('Error loading course creation form!', [$e]);

            return redirect()->back()->with('error', 'Failed to load course creation form.');
        }
    }

    /**
     * Show the specified resource.
     *
     * @param  Courses $singularTableName
     * @return \Illuminate\View\View
     */

    public function store(StoreCourseRequest $request):JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            // dd('store method in CourseController');

            // Handle feature video upload
            $featureVideoPath = null;
            if ($request->hasFile('feature_video')) {
                $featureVideoPath = $request->file('feature_video')->store('courses/feature-videos', 'public');
            }

            // Handle feature image upload
            $featureImagePath = null;
            if ($request->hasFile('feature_image')) {
                $featureImagePath = $request->file('feature_image')->store('courses/feature-images', 'public');
            }

            // Create course
            $course = Course::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'level' => $request->level,
                'price' => $request->price,
                'feature_video_path' => $featureVideoPath,
                'feature_image_path' => $featureImagePath,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create modules and contents
            foreach ($request->modules as $moduleData) {
                $module = $course->courseModules()->create([
                    'title' => $moduleData['title'],
                    'description' => $moduleData['description'] ?? null,
                    'order' => $moduleData['order'] ?? 0,
                    'is_published' => true,
                ]);

                // Create contents for this module
                if (isset($moduleData['contents']) && is_array($moduleData['contents'])) {
                    foreach ($moduleData['contents'] as $contentData) {
                        $contentDataArray = $this->processContentData($contentData);

                        $module->contents()->create([
                            'content_type_id' => $contentData['content_type_id'],
                            'title' => $contentData['title'],
                            'content_data' => $contentDataArray,
                            'order' => $contentData['order'] ?? 0,
                            'estimated_duration' => $contentData['estimated_duration'] ?? 0,
                            'is_published' => true,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Course created successfully!', 'course_id' => $course->id], 201);
            }

            return redirect()->route('admin.courses.index')
                ->with('success', 'Course created successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Course creation failed: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());

            dd('Error: ' . $e->getMessage());

            return back()->with('error', 'Failed to create course. Please try again. Error: ' . $e->getMessage())
                ->withInput();
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
