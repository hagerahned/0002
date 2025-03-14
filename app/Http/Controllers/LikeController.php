<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function add(Request $request)
    {
        // validate on post slug
        $request->validate([
            'post_slug' => 'required|exists:posts,slug'
        ]);
        // fetch post
        $post = Post::where('slug', $request->post_slug)->first();
        if (!$post) {
            return ApiResponse::sendResponse('Post not found', [], false);
        }
        // if user make like on post remove it
        if ($post->likes()->where('user_id', $request->user()->id)->exists()) {
            $post->likes()->delete();
            return ApiResponse::sendResponse('Like removed', [], true);
        }
        // else add like
        $post->likes()->create([
            'user_id' => $request->user()->id
        ]);
        return ApiResponse::sendResponse('Like added', [], true);
    }
}
