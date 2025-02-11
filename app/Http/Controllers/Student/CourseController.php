<?php

namespace App\Http\Controllers\Student;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function enroll(Request $request){
        $request->validate([
            'course_slug' => 'required|exists:courses,slug'
        ]);

        // enroll the student in the course
        $course = Course::where('slug',$request->course_slug)->first();
        // check if course exist
        if (!$course){
            return ApiResponse::sendResponse('course not found',[]);
        }
        // check if student is already enrolled in the course
        if ($course->students()->where('user_id',$request->user()->id)->exists()){
            return ApiResponse::sendResponse('You are already enrolled in this course',[]);
        }
        // check if the course enrollment not started
        if($course->apply_start > now()){
            return ApiResponse::sendResponse('Enrollment period has not started yet',[]);
        }
        // check if enrollment period has ended
        if($course->apply_end < now()){
            return ApiResponse::sendResponse('Enrollment period has ended',[]);
        }
        
        $course->students()->attach($request->user()->id);
        return ApiResponse::sendResponse('Course enrolled successfully',[]);
    }
}
