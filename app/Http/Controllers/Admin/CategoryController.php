<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\StoreCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request)
    {
        // check if category not exists
        $category = Category::where('slug', $request->slug)->first();
        if (!$category) {
            // create new category
            $category = Category::create([
                'name' => $request->name,
                'slug' => Slug::makeCourse(new Category(),$request->name),
                'manager_id' => $request->user()->id,
            ]);
            return ApiResponse::sendResponse('Category created successfully', new StoreCategoryResource($category), true);
        }
        return ApiResponse::sendResponse('category already exists', [], false);
    }

    public function update(UpdateCategoryRequest $request){
        // check if category exists
        $category = Category::where('slug', $request->category_slug)->first();
        if (!$category) {
            return ApiResponse::sendResponse('Category not found', [], false);
        }

        // update category
        $category->update([
            'name' => $request->name,
            'slug' => Slug::makeCourse(new Category(),$request->name),
        ]);

        return ApiResponse::sendResponse('Category updated successfully', new StoreCategoryResource($category), true);
    }

    public function show(Request $request){
        $request->validate([
            'category_slug' => 'required|exists:categories,slug'
        ]);
        $category = Category::where('slug', $request->category_slug)->first();
        if (!$category) {
            return ApiResponse::sendResponse('Category not found', [],false);
        }
        return ApiResponse::sendResponse('Category found', new StoreCategoryResource($category),true);
    }
}
