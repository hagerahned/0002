<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\StorePostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $slug = Slug::makeCourse(new Post(), $request->title);

        // store post image
        $image = $request->image;
        $extension = $image->getClientOriginalExtension();
        $path = 'public/images/posts/';
        $imageName = $path . uuid_create() . '.' . $extension;
        $newImage = $image->move('images/posts', $imageName);

        // create new post
        $post = Post::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'image' => $newImage,
            'manager_id' => $request->user()->id,
        ]);

        return ApiResponse::sendResponse('Post Created Successfully', new StorePostResource($post));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $request->validate([
            'post_slug' => 'required|exists:posts,slug'
        ]);
        $input = $request->post_slug;
        $post = Post::where('slug', $input)->first();
        if (!$post) {
            return ApiResponse::sendResponse('Post not found', []);
        }
        return ApiResponse::sendResponse('Post found', new StorePostResource($post));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        //
    }
}
