<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionDetailResource extends JsonResource
{

    private $type;
    public function __construct($resource, $type = 'topic')
    {
        parent::__construct($resource);
        $this->type = $type;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // dd($this->translations);

        $lite = [
            'id' => $this->id,
            'order_index' => $this->order_index,
            'file' => storage_url($this->file),
            'status' => $this->status_label,
            'statusList' => $this->status_list,
            'questions_count' => $this->questions_count ?? 0,
            'language' => translation($this->translations)?->language ?? "Not Found",
            'title' => translation($this->translations)?->title ?? "Not Found",
            'description' => translation($this->translations)?->description ?? "Not Found",
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];


        $relations = ['topic' => new TopicResource($this->whenLoaded('topic'), 'lite')];

        // if ($this->practice) {
        // $totalQuestions = $this->questions_count ?? 0;
        // $totalAttempts = $this->practice->total_attempts ?? 0;
        // $wrongAttempts = $this->practice->wrong_attempts ?? 0;
        // $correctAttempts = $this->practice->correct_attempts ?? 0;


        // $progress = $totalQuestions > 0
        //     ? min(100, round(($correctAttempts / $totalQuestions) * 100, 2))
        //     : 0;

        // $progressStatus = match (true) {
        //     $totalAttempts === 0 => 'Not Started',
        //     $progress >= 100 => 'Completed',
        //     default => 'In Progress',
        // };

        // $practices = [
        //     'total_attempts' => $this->practice && $this->practice->total_attempts ? $this->practice->total_attempts : 0,
        //     'correct_attempts' => $this->practice && $this->practice->correct_attempts ? $this->practice->correct_attempts : 0,
        //     'wrong_attempts' => $this->practice && $this->practice->wrong_attempts ? $this->practice->wrong_attempts : 0,
        //     'progress' => $this->practice && $this->practice->progress ? $this->practice->progress : 0,
        //     'progress_status' => $this->practice && $this->practice->status ? $this->practice->status_label : 'Not Started',
        // ];
        // } else {
        //     $practices = [
        //         'totalAttempts' => 0,
        //         'correctAttempts' => 0,
        //         'wrongAttempts' => 0,
        //         'progress' => 0,
        //         'progressStatus' => 'Not Started',
        //     ];
        // }



        // $practice = new PracticeQuestionResource($this->whenLoaded('practice'));
        // $practices = ['practice' => $practice, 'progress' => $this->questions->count() - $practice->attempts];
        // }


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
