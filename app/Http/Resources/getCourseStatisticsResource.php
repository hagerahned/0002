<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class getCourseStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "Attendance" => [
                'Status' => $this->pivot->status ?? 'unknown',
                'Attended At' => $this->pivot->created_at->format('Y-m-d H:i') ?? null,
            ]

        ];
    }
}
