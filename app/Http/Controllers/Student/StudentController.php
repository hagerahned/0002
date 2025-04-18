<?php

namespace App\Http\Controllers\Student;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInterestsRequest;
use App\Http\Resources\StoreCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function storeInterests(StoreInterestsRequest $request)
    {
        $slugs = $request->input('interests');
        $categoryIds = Category::whereIn('slug', $slugs)->pluck('id')->toArray();
        $user = $request->user();
        $user->interests()->sync($categoryIds);

        return ApiResponse::sendResponse('Interests Stored Successfully', StoreCategoryResource::collection($user->interests), true);
    }
}
