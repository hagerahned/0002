<?php

namespace App\Http\Controllers\Student;

use App\Helpers\ApiResponse;
use App\Helpers\Slug;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\StoreCommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {

        // fetch post
        $post = Post::where('slug', $request->post_slug)->first();

        // create slug
        $slug = Str::uuid();
        // create comment
        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'slug' => $slug
        ]);

        return ApiResponse::sendResponse("Comment created successfully", new StoreCommentResource($comment), true);
    }
}
