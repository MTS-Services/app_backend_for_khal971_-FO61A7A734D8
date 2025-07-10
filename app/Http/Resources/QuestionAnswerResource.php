<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
{

    private $type;
    public function __construct($resource, $type = 'question_answer')
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
        $lite = [
            'id' => $this->id,
            'order_index' => $this->order_index,
            'question_id' => $this->question_id,
            'user_id' => $this->user_id,
            'language' => translation($this->translations)->language ?? 'Not Found',
            'answer' => translation($this->translations)->answer ?? 'Not Found',
            'match_percentage' => translation($this->translations)->match_percentage ?? 0,
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
        ];

        $audits = [
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];
        $relations = ['question' => new QuestionResource($this->whenLoaded('question'), 'lite')];
        return match ($this->type) {
            'lite' => $lite,
            default => array_merge($lite, $audits, $relations),
        };
    }
}
