<?php

namespace App\Http\Controllers\Instructor;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Resources\StoreAttendanceResource;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(StoreAttendanceRequest $request)
    {
        // fetch student
        $student = User::where('role','student')->where('email', $request->user_email)->first();

        // fetch course
        $course = Course::whereHas('students', function ($query) use ($student) {
            $query->where('users.id', $student->id);
        })->first();

        // check if the course enrollment not started
        if ($course->apply_start > now()) {
            return ApiResponse::sendResponse('Course not started yet.', [], false);
        }

        // check if student accepted in course
        if (!$course->students()->where('user_id', $student->id)->wherePivot('status', 'accepted')->exists()) {
            return ApiResponse::sendResponse('Student not enrolled in the course or not accepted.', [], false);
        }

        // prevent duplicate data
        $oldAttendance = Attendance::where('user_id', $student->id)->where('course_id', $course->id)->first();

        if ($oldAttendance) {
            if ($oldAttendance->status != $request->status) {
                $oldAttendance->update(['status' => $request->status]);
                return ApiResponse::sendResponse('Attendance status updated successfully.',  new StoreAttendanceResource($oldAttendance), true);
            }
            return ApiResponse::sendResponse('Attendance already recorded for this student.', [], false);
        }
        

        // store attendance
        $attendance = new Attendance();
        $attendance->user_id = $student->id;
        $attendance->course_id = $course->id;
        $attendance->instructor_id = $request->user()->id;
        $attendance->status = $request->status;
        $attendance->save();

        return ApiResponse::sendResponse('Attendance recorded successfully.', new StoreAttendanceResource($attendance), true);
    }
}
