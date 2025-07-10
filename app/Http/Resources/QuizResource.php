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


        if ($this->practice) {
            $totalQuestions = $this->questions_count ?? 0;
            $totalAttempts = $this->practice->total_attempts ?? 0;
            $wrongAttempts = $this->practice->wrong_attempts ?? 0;
            $correctAttempts = $this->practice->correct_attempts ?? 0;


            $progress = $totalQuestions > 0
                ? min(100, round(($correctAttempts / $totalQuestions) * 100, 2))
                : 0;

            $progressStatus = match (true) {
                $totalAttempts === 0 => 'Not Started',
                $progress >= 100 => 'Completed',
                default => 'In Progress',
            };

            $practices = [
                'totalAttempts' => $totalAttempts,
                'correctAttempts' => $correctAttempts,
                'wrongAttempts' => $wrongAttempts,
                'progress' => $progress,
                'progressStatus' => $progressStatus,
            ];
        } else {
            $practices = [
                'totalAttempts' => 0,
                'correctAttempts' => 0,
                'wrongAttempts' => 0,
                'progress' => 0,
                'progressStatus' => 'Not Started',
            ];
        }

        return match ($this->type) {
            'lite' => $lite,
            'practice' => array_merge($lite, $practices),
            default => array_merge($lite, $practices, $relations),
        };
    }
}
