<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lite =  [
            'id'                => $this->id,
            'order_index'       => $this->order_index,
            'question_id'       => $this->question_id,
            'user_id'           => $this->user_id,
            'match_percentage'  => $this->match_percentage,
            'language'          => translation($this->translations)->language ?? 'en',
            'answer'            => translation($this->translations)->answer ?? 'Not Found',
            'created_at'        => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at'        => $this->updated_at_formatted ?? "N/A",
            'created_by'        => $this->creater?->name ?? "System",
            'updated_by'        => $this->updater?->name ?? "N/A",
        ];
        $relations = ['question' => new QuestionResource($this->whenLoaded('question'), 'lite')];
        return array_merge($lite, $relations);
    }
}