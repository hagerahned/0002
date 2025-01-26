<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Resources\StoreCourseResource;
use App\Models\Course;
use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    
    public function store(StoreCourseRequest $requset){
        $slug = Slug::makeCourse(new Course,$requset->title);
        $instructor = Instructor::where('email',$requset->instructor_email)->first();
        // store course image
        $image = $requset->image;
        $extension = $image->getClientOriginalExtension();
        $path = 'public/images/courses/';
        $imageName = $path . uuid_create() . '.' .$extension;
        $newImage = $image->move('images/courses', $imageName);
        
        if(!$instructor){
            return ApiResponse::sendResponse('Instructor not found',[]);
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
}
