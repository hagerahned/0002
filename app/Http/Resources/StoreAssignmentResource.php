<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreAssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Assignment' => [
                'Title' => $this->name,
                'Slug' => $this->slug,
                'Instructor' => new StoreInstructorResource($this->instructor),
                'Course' => new StoreCourseResource($this->course),
                'Files' => [
                    StoreAssignmentFilesResource::collection($this->files),
                ],
            ]
        ];
    }
}
