<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\RetriveStudentsResource;
use App\Http\Resources\StoreCourseResource;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class CourseController extends Controller
{

    public function store(StoreCourseRequest $request)
    {
        $slug = Slug::makeCourse(new Course, $request->title);
        $instructor = Instructor::where('email', $request->instructor_email)->first();

        // store course image
        $image = $request->image;
        $extension = $image->getClientOriginalExtension();
        $path = 'public/images/courses/';
        $imageName = $path . uuid_create() . '.' . $extension;
        $newImage = $image->move('images/courses', $imageName);

        if (!$instructor) {
            return ApiResponse::sendResponse('Instructor not found', []);
        }
        if ($instructor->course_id != null) {
            return ApiResponse::sendResponse('Instructor already has course', []);
        }

        // Create new course
        $course = Course::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'manager_id' => $request->user()->id,
            'image' => $newImage,
            'course_start' => Carbon::parse($request->course_start),
            'course_end' => Carbon::parse($request->course_end),
            'apply_start' => Carbon::parse($request->apply_start),
            'apply_end' => Carbon::parse($request->apply_end),
            'location' => $request->location,
        ]);

        // add instructor to course
        $instructor->update([
            'course_id' => $course->id
        ]);

        return ApiResponse::sendResponse('Course created successfully', new StoreCourseResource($course));
    }

    public function update(UpdateCourseRequest $request)
    {
        if (!empty($request)) {
            $course = Course::where('slug', $request->course_slug)->first();
            if (!$course) {
                return ApiResponse::sendResponse('Course not found', []);
            }
            $instructor = Instructor::where('email', $request->instructor_email)->first();
            if ($request->hasFile('image')) {
                $image = $request->image;
                $extension = $image->getClientOriginalExtension();
                $path = 'public/images/courses/';
                $imageName = $path . uuid_create() . '.' . $extension;
                $newImage = $image->move('images/courses', $imageName);
            }
            // Create new course
            $course->update([
                'title' => $request->title ?? $course->title,
                'slug' => $request->course_slug,
                'description' => $request->description ?? $course->description,
                'manager_id' => $request->user()->id,
                'image' => $newImage ?? $course->image,
                'course_start' => Carbon::parse($request->course_start) ?? $course->course_start,
                'course_end' => Carbon::parse($request->course_end) ?? $course->course_end,
                'apply_start' => Carbon::parse($request->apply_start) ?? $course->apply_start,
                'apply_end' => Carbon::parse($request->apply_end) ?? $course->apply_end,
                'location' => $request->location ?? $course->location,
            ]);

            // add instructor to course
            $instructor->update([
                'course_id' => $course->id
            ]);

            return ApiResponse::sendResponse('Course updated successfully', new StoreCourseResource($course));
        }

        return ApiResponse::sendResponse('No data provided', []);
    }

    public function show(Request $request)
    {
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

    public function delete(request $request)
    {
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

    public function restore(request $request)
    {
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

    public function getAllEnrollmentStudents(Request $request)
    {
        $request->validate([
            'course_slug' => 'required|exists:courses,slug'
        ]);
        $input = $request->course_slug;
        $course = Course::where('slug', $input)->first();
        $students = $course->students;
        if (!$course) {
            return ApiResponse::sendResponse('Course not found', []);
        }
        if (!$students) {
            return ApiResponse::sendResponse('No Students Enrolled', []);
        }
        return ApiResponse::sendResponse('Student Retrived Successfuly', RetriveStudentsResource::collection($students));
    }

    public function acceptStudent(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'course_slug' => 'required|exists:courses,slug'
        ]);

        $user = User::where('email', $request->email)->first();
        $course = Course::where('slug', $request->course_slug)->first();

        if (!$user) {
            return ApiResponse::sendResponse('User not found', []);
        }

        if (!$course) {
            return ApiResponse::sendResponse('Course not found', []);
        }

        // Check if the student is already enrolled
        $student = $course->students()->where('user_id', $user->id)->first();

        if (!$student) {
            return ApiResponse::sendResponse('Student not enrolled in the course', []);
        }

        // Update the pivot status to 'accepted'
        $course->students()->updateExistingPivot($user->id, ['status' => 'accepted']);

        return ApiResponse::sendResponse('Student status updated to accepted', []);
    }
}
