<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Student' => [
                'Student Name' => $this->user->name,
                'Student Email' => $this->user->email,
            ],
            'Course' => [
                'Course Title' => $this->course->title,
            ],
            'Attendance' => $this->status
        ];
    }
}
