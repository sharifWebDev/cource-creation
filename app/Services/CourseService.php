<?php
// app/Services/CourseService.php

namespace App\Services;

use App\Models\Course;
use App\DTOs\CourseDto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\DTOs\CourseModuleDto;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\DTOs\CourseModuleContentDto;
use App\Http\Resources\BaseResource;
use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\CourseNotFoundException;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\Contracts\CourseModuleRepositoryInterface;
use App\Repositories\Contracts\CourseModuleContentRepositoryInterface;

class CourseService
{
    public function __construct(
        protected CourseRepositoryInterface $courseRepository,
        protected CourseModuleRepositoryInterface $moduleRepository,
        protected CourseModuleContentRepositoryInterface $contentRepository,
        protected FileService $fileService,
    ) {}

    public function get(Request $request)
    {
        $this->courseRepository->with(['category', 'author']);
        $courses = $this->courseRepository->paginateFiltered($request);
        $resource = new BaseResource($courses, CourseResource::class);
        return $resource->toArray(request());
    }

    public function getAll()
    {
        return $this->courseRepository->all();
    }

    public function getById(int $id): Course
    {
        $course = $this->courseRepository->find($id);
        if (!$course) {
            throw new CourseNotFoundException();
        }
        return $course;
    }


    public function create(CourseDto $dto, Request $request): Course
    {
        return DB::transaction(function () use ($dto, $request) {

            $data = $this->handleCourseFiles($dto->toArray(), $request);

            $course = $this->courseRepository->create($data);

            if (!empty($dto->modules)) {
                $this->createCourseModules($course->id, $dto->modules);
            }

            return $course->load(['courseModules.contents']);
        });
    }


    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {

            $current = $this->courseRepository->findForUpdate($id);

            if (!$current) {
                throw new CourseNotFoundException();
            }

            $this->deleteCourseFiles($current);

            return $this->courseRepository->delete($id);
        });
    }

    private function createCourseModules(int $courseId, array $modules): void
    {
        foreach ($modules as $moduleData) {
            $moduleDto = CourseModuleDto::fromArray(array_merge($moduleData, [
                'course_id' => $courseId
            ]));

            $module = $this->moduleRepository->create($moduleDto->toArray());

            if (!empty($moduleData['contents'])) {
                $this->createModuleContents($module->id, $moduleData['contents']);
            }
        }
    }


    private function createModuleContents(int $moduleId, array $contents): void
    {
        foreach ($contents as $contentData) {

            $contentData = $this->handleContentFiles($contentData);

            $contentDto = CourseModuleContentDto::fromArray(array_merge($contentData, ['course_module_id' => $moduleId]));

            $this->contentRepository->create($contentDto->toArray());
        }
    }

    private function deleteCourseFiles(Course $course): void
    {
        foreach ($course->courseModules as $module) {
            foreach ($module->contents as $content) {

                $data = $content->content_data ?? [];

                $contentType = $content->contentType->slug ?? null;

                $fileTypes = ['image', 'video', 'document', 'file'];

                if (in_array($contentType, $fileTypes, true)) {

                    $paths = array_filter([
                        $data['url'] ?? null,
                        $data['image_path'] ?? null,
                        $data['video_path'] ?? null,
                        $data['document_path'] ?? null,
                    ]);

                    foreach ($paths as $path) {
                        if ($path && !preg_match('/^https?:\/\//', $path)) {
                            try {
                                if (Storage::exists($path)) {
                                    Storage::delete($path);
                                }
                            } catch (\Throwable $e) {
                                Log::warning("Failed to delete file for content ID {$content->id}: {$e->getMessage()}");
                            }
                        }
                    }
                }
            }
        }
    }

    public function handleCourseFiles(array $data, Request $request): array
    {
        $uploaded = [];

        if ($request->hasFile('feature_video_path')) {
            $uploaded['feature_video_path'] = $request
                ->file('feature_video_path')
                ->store('courses/feature-videos', 'public');
        }

        if ($request->hasFile('feature_video_thumbnail')) {
            $uploaded['feature_video_thumbnail'] = $request
                ->file('feature_video_thumbnail')
                ->store('courses/feature-images', 'public');
        }

        return array_merge($data, $uploaded);
    }

    protected function handleContentFiles(array $contentData): array
    {
        $contentTypeId = $contentData['content_type_id'] ?? null;

        if (!$contentTypeId) {
            return $contentData;
        }

        $contentType = \App\Models\ContentType::find($contentTypeId);
        if (!$contentType) {
            return $contentData;
        }

        $files = $contentData['content_data'] ?? [];

        switch ($contentType->slug) {
            case 'video':
                if (!empty($files['video_file'])) {
                    $files['video_path'] = $this->fileService->storeContentVideo($files['video_file']);
                }
                break;
            case 'image':
                if (!empty($files['image_file'])) {
                    $files['image_path'] =  $this->fileService->storeContentImage($files['image_file']);
                }
                break;

            case 'document':
                if (!empty($files['document_file'])) {
                    $files['file_path'] = $this->fileService->storeContentDocument($files['document_file']);
                }
                break;
        }
        return array_merge($contentData, ['content_data' => $files]);
    }
}
