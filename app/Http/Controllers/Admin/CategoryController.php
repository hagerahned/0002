<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\StoreCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request)
    {
        // check if category not exists
        $category = Category::where('name', $request->name)->first();
        if (!$category) {
            // create new category
            $category = Category::create([
                'name' => $request->name,
                'manager_id' => $request->user()->id,
            ]);
            return ApiResponse::sendResponse('Category created successfully', new StoreCategoryResource($category), true);
        }
        return ApiResponse::sendResponse('category already exists', [], false);
    }
}
