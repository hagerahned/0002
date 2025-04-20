<?php

namespace App\Http\Controllers\Student;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\getCourseStatisticsResource;
use App\Http\Resources\StoreCourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function getAvailableCourses()
    {
        $courses = Course::where('apply_end', '>', now())->get();
        return ApiResponse::sendResponse('Course Retrived successfully', StoreCourseResource::collection($courses), true);
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'course_slug' => 'required|exists:courses,slug'
        ]);

        // check if the user is already in course
        if ($request->user()->is_enrolled == 'yes') {
            return ApiResponse::sendResponse('You are already enrolled in course', [], false);
        }

        $course = Course::where('slug', $request->course_slug)->first();
        // check if course exist
        if (!$course) {
            return ApiResponse::sendResponse('course not found', [], false);
        }
        // check if the course enrollment not started
        if ($course->apply_start > now()) {
            return ApiResponse::sendResponse('Enrollment period has not started yet', [], false);
        }
        // check if enrollment period has ended
        if ($course->apply_end < now()) {
            return ApiResponse::sendResponse('Enrollment period has ended', [], false);
        }

        $course->students()->attach($request->user()->id);
        return ApiResponse::sendResponse('Course enrolled successfully', [], true);
    }

    public function getCourseStatistics(Request $request)
    {
        $request->validate([
            'course_slug' => 'required|exists:courses,slug'
        ]);

        // check if the user is in course
        if ($request->user()->is_enrolled == 'no') {
            return ApiResponse::sendResponse('You are not enrolled in course', [], false);
        }

        $course = Course::where('slug', $request->course_slug)->first();
        $user = Auth::user();

        // get student attendance in course
        $data = $course->attendances->where('id', $user->id);
        return ApiResponse::sendResponse('Stistics Retrived Successfully.', getCourseStatisticsResource::collection($data), true);
    }
}
