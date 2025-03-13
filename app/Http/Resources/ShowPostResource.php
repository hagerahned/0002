<?php

namespace App\Http\Resources;

use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Post' => new StorePostResource($this),
            'Comments' => StoreCommentResource::collection($this->comments),
        ];
    }
}
