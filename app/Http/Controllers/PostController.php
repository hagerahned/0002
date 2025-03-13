<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\GetAllPostsResource;
use App\Http\Resources\ShowPostResource;
use App\Http\Resources\StorePostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(1);
        if (count($posts) > 0) {
            if ($posts->total() > $posts->perPage()) {
                $data = [
                    'Posts' => StorePostResource::collection($posts),
                    'Pagination Links' => [
                        'Current Page' => $posts->currentPage(),
                        'Per Page' => $posts->perPage(),
                        'Total Pages' => $posts->lastPage(),
                        'Links' => [
                            'First Page URL' => $posts->url(1),
                            'Last Page URL' => $posts->url($posts->lastPage()),
                            'Next Page URL' => $posts->nextPageUrl(),
                            'Previous Page URL' => $posts->previousPageUrl(),
                        ],
                    ]
                ];
            } else {
                $data = [
                    'Posts' => StorePostResource::collection($posts),
                ];
            }
            return ApiResponse::sendResponse('Posts Retrieved Successfully', $data, true);
        }
        return ApiResponse::sendResponse('No Posts Found', [], false);
    }

    public function show(Request $request){
        $request->validate([
            'post_slug' =>'required|exists:posts,slug'
        ]);
        $input = $request->post_slug;
        $post = Post::where('slug', $input)->first();
        if (!$post) {
            return ApiResponse::sendResponse('Post not found', [], false);
        }
        return ApiResponse::sendResponse('Post found', new ShowPostResource($post), true);
    }
}
