<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstructorRequest;
use App\Http\Requests\UpdateInstructorRequest;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\StoreInstructorResource;
use App\Http\Traits\SlugUsername;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\Slug;
use App\Models\User;
use App\Service\FileUploadService;
use Illuminate\Support\Facades\Auth;

class InstructorController extends Controller
{
    protected $fileUploadService;
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function store(StoreInstructorRequest $request)
    {
        $username = Slug::makeUser(new User(), $request->name);
        // create a new instructor
        $instructor = User::where('role', 'instructor')->create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' =>  bcrypt($request->password),
            'phone' => $request->phone,
            'manager_id' => $request->user()->id,
            'role' => 'instructor'
        ]);

        // Handle file upload if a file is provided
        $image = $request->file('image');
        $path = $this->fileUploadService->uploadImage($image);
        $instructor->image = $path;
        $instructor->save();

        return ApiResponse::sendResponse('instructor created successfully', new StoreInstructorResource($instructor), true);
    }

    public function show(Request $request)
    {
        $input = $request->username;
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $instructor = User::where('email', $input)->first();
        } else {
            $instructor = User::where('username', $input)->first();
        }

        if ($instructor) {
            return ApiResponse::sendResponse('instructor created successfully', new InstructorResource($instructor), true);
        } else {
            return ApiResponse::sendResponse('instructor not found', [], false);
        }
    }

    public function update(UpdateInstructorRequest $request)
    {
        if ($request->has('email')) {
            $instructor = User::where('email', $request->email)->first();
            if (!$instructor) {
                $instructor = User::where('username', $request->username)->first();
            }
        }

        $instructor->update([
            'name' => $request->name ?? $instructor->name,
            'email' => $request->email ?? $instructor->email,
            'username' => $request->username ?? $instructor->username,
            'phone' => $request->phone ?? $instructor->phone,
            'description' => $request->description ?? $instructor->description,
            'password' => bcrypt($request->password) ?? $instructor->password,
        ]);

        if ($request->hasFile('image')) {
            // delete previous image
            $this->fileUploadService->deleteImage($instructor->image);
            $instructor->image = null;
            $instructor->save();
            
            // Handle file upload if a file is provided
            $image = $request->file('image');
            $path = $this->fileUploadService->uploadImage($image);
            $instructor->image = $path;
            $instructor->save();
        }

        return ApiResponse::sendResponse('instructor updated successfully', new StoreInstructorResource($instructor), true);
    }

    public function delete(Request $request)
    {
        $input = $request->username;
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $instructor = User::where('email', $input)->first();
        } else {
            $instructor = User::where('username', $input)->first();
        }

        if ($instructor) {
            $instructor->tokens()->delete();
            $instructor->delete();
            return ApiResponse::sendResponse('instructor deleted successfully', [], true);
        } else {
            return ApiResponse::sendResponse('instructor not found', [], false);
        }
    }

    public function restore(Request $request)
    {
        $input = $request->username;
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $instructor = User::onlyTrashed()->where('email', $input)->first();
        } else {
            $instructor = User::onlyTrashed()->where('username', $input)->first();
        }

        if ($instructor) {
            $instructor->restore();
            return ApiResponse::sendResponse('instructor restored successfully', [], true);
        } else {
            return ApiResponse::sendResponse('instructor not found', [], false);
        }
    }
}
