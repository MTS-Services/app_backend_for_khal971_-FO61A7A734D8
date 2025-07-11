<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lite = [
            'id'          => $this->id,
            'order_index' => $this->order_index,
            'topic_id'    => $this->topic_id,
            'status'      => $this->status_label,
            'statusList'  => $this->status_list,
            'language'    => translation($this->translations)->language ?? 'en',
            'title'       => translation($this->translations)->title ?? 'Not Found',
            'description' => translation($this->translations)->description ?? 'Not Found',
            'created_at'  => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at'  => $this->updated_at_formatted ?? "N/A",
            'created_by'  => $this->creater?->name ?? "System",
            'updated_by'  => $this->updater?->name ?? "N/A",
        ];
        $relations = ['topics' => new TopicResource($this->whenLoaded('topics'), 'lite')];

        $practices = [
            'total_attempts' => $this->practice && $this->practice->total_attempts ? $this->practice->total_attempts : 0,
            'correct_attempts' => $this->practice && $this->practice->correct_attempts ? $this->practice->correct_attempts : 0,
            'wrong_attempts' => $this->practice && $this->practice->wrong_attempts ? $this->practice->wrong_attempts : 0,
            'progress' => $this->practice && $this->practice->progress ? $this->practice->progress : 0,
            'progress_status' => $this->practice && $this->practice->status ? $this->practice->status_label : 'Not Started',
        ];

        return match ($this->type) {
            'lite' => $lite,
            'practice' => array_merge($lite, $practices),
            default => array_merge($lite, $practices, $relations),
        };
    }
}
