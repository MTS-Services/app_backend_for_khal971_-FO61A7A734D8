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
        if ($this->practice) {
            $practice = ['practice' => new PracticeQuestionResource($this->whenLoaded('practice'))];
        } else {
            $practice = ['practice' => 'Not Found'];
        }


        return match ($this->type) {
            'lite' => $lite,
            'practice' => array_merge($lite, $practice),
            default => array_merge($lite, $relations),
        };
    }
}
