<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAllPostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'current_page' => $this->currentPage(),
            'data' => new StorePostResource($this),
            // 'last_page' => $this->lastPage(),
            // 'total' => $this->total(),
            // 'per_page' => $this->perPage(),
            // 'next_page_url' => $this->nextPageUrl(),
            // 'prev_page_url' => $this->previousPageUrl(),
        ];
    }
}
