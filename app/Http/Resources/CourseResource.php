<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{

    private $type;
    public function __construct($resource, $type = 'course')
    {
        parent::__construct($resource);
        $this->type = $type;
    }

    public function toArray(Request $request): array
    {
        $lite = [
            'id' => $this->id,
            'order_index' => $this->order_index,
            'icon' => storage_url($this->subject?->icon),
            'status' => $this->status_label,
            'statusList' => $this->status_list,
            'topics_count' => $this->topics_count ?? 0,
            'questions_count' => $this->questions_count ?? 0,
            'quizzes_count' => $this->quizzes_count ?? 0,
            'language' => translation($this->translations)?->language ?? "Not Found",
            'name' => translation($this->translations)?->name ?? "Not Found",
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];
        $relations = ['subject' => new SubjectResource($this->whenLoaded('subject'))];

        $fetchedPractice = $this->relationLoaded('practice') && !empty($this->practice);
        $practices = [
            'total_attempts'   => $fetchedPractice ? ($this->practice->total_attempts ?? 0) : 0,
            'correct_attempts'    => $fetchedPractice ? ($this->practice->correct_attempts ?? 0) : 0,
            'wrong_attempts'      => $fetchedPractice ? ($this->practice->wrong_attempts ?? 0) : 0,
            'progress'         => $fetchedPractice ? ($this->practice->progress ?? 0) : 0,
            'progress_status'  => $fetchedPractice ? ($this->practice->status_label ?? 'Not Started') : 'Not Started',
        ];


        return match ($this->type) {
            'lite' => $lite,
            'relations' => array_merge($lite, $relations),
            'practice' => array_merge($lite, $practices),
            default => array_merge($lite, $practices, $relations),
        };
    }
}
