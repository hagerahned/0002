<?php

namespace App\Http\Controllers\Instructor;

use App\Helpers\ApiResponse;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Resources\StoreAssignmentResource;
use App\Models\Assignment;
use App\Models\Course;
use App\Service\FileUploadService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    public function store(StoreAssignmentRequest $request)
    {
        $instructor = $request->user();
        $course = Course::where('slug', $request->course_slug)->firstOrFail();

        // Check if the instructor teaches this course
        if ($instructor->course_id != $course->id) {
            return ApiResponse::sendResponse('You are not authorized to add an assignment for this course', [], false);
        }

        // Create assignment first
        $assignment = $course->assignments()->create([
            'name' => $request->name,
            'slug' => Slug::makeCourse(new Assignment(), $request->name),
            'instructor_id' => $instructor->id
        ]);

        // Handle file upload if a file is provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Ensure it's always an array before passing to the service
            $paths = $this->fileUploadService->uploadFiles($file);


            // Store file path in DB
            foreach ($paths as $path) {

                $assignment->files()->create([
                    'file' => $path
                ]);
            };
            $assignment->save();
        }

        return ApiResponse::sendResponse('Assignment created successfully', new StoreAssignmentResource($assignment), true);
    }
}
