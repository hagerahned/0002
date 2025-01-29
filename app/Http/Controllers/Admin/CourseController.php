<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\StoreCourseResource;
use App\Models\Course;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Pest\Laravel\put;

class CourseController extends Controller
{

    public function store(StoreCourseRequest $requset)
    {
        $slug = Slug::makeCourse(new Course, $requset->title);
        $instructor = Instructor::where('email', $requset->instructor_email)->first();
        // store course image
        $image = $requset->image;
        $extension = $image->getClientOriginalExtension();
        $path = 'public/images/courses/';
        $imageName = $path . uuid_create() . '.' . $extension;
        $newImage = $image->move('images/courses', $imageName);

        if (!$instructor) {
            return ApiResponse::sendResponse('Instructor not found', []);
        }
        // Create new course
        $course = Course::create([
            'title' => $requset->title,
            'slug' => $slug,
            'description' => $requset->description,
            'manager_id' => $requset->user()->id,
            'image' => $newImage,
            'start_at' => Carbon::parse($requset->start_at),
            'end_at' => Carbon::parse($requset->end_at),
        ]);

        // add instructor to course
        $instructor->update([
            'course_id' => $course->id
        ]);

        return ApiResponse::sendResponse('Course created successfully', new StoreCourseResource($course));
    }

    public function update(UpdateCourseRequest $requset)
    {
        if (!empty($requset)) {
            $course = Course::where('slug', $requset->course_slug)->first();
            if (!$course) {
                return ApiResponse::sendResponse('Course not found', []);
            }
            $instructor = Instructor::where('email', $requset->instructor_email)->first();
            if ($requset->hasFile('image')) {
                $image = $requset->image;
                $extension = $image->getClientOriginalExtension();
                $path = 'public/images/courses/';
                $imageName = $path . uuid_create() . '.' . $extension;
                $newImage = $image->move('images/courses', $imageName);
            }
            // Create new course
            $course->update([
                'title' => $requset->title ?? $course->title,
                'slug' => $requset->course_slug,
                'description' => $requset->description ?? $course->description,
                'manager_id' => $requset->user()->id,
                'image' => $newImage,
                'start_at' => Carbon::parse($requset->start_at),
                'end_at' => Carbon::parse($requset->end_at),
            ]);

            // add instructor to course
            $instructor->update([
                'course_id' => $course->id
            ]);

            return ApiResponse::sendResponse('Course updated successfully', new StoreCourseResource($course));
        }

        return ApiResponse::sendResponse('No data provided', []);
    }

    public function show(Request $request){
        $request->validate([
            'course_slug' => 'required|exists:courses,slug'
        ]);
        $input = $request->course_slug;
        $course = Course::where('slug', $input)->first();
        if (!$course) {
            return ApiResponse::sendResponse('Course not found', []);
        }
        return ApiResponse::sendResponse('Course found', new StoreCourseResource($course));
    }

    public function delete(Request $request){
        $request->validate([
            'course_slug' => 'required|exists:courses,slug'
        ]);
        $input = $request->course_slug;
        $course = Course::where('slug', $input)->first();
        if (!$course) {
            return ApiResponse::sendResponse('Course not found', []);
        }
        $course->instructor->course_id = null;
        $course->instructor->save();
        $course->delete();
        return ApiResponse::sendResponse('Course Deleted Successfuly', []);
    }

    public function restore(Request $request){
        $request->validate([
            'course_slug' => 'required|exists:courses,slug'
        ]);
        $input = $request->course_slug;
        $course = Course::onlyTrashed()->where('slug', $input)->first();
        if (!$course) {
            return ApiResponse::sendResponse('Course not found', []);
        }
        $course->restore();
        return ApiResponse::sendResponse('Course restored successfully', []);
    }

}
