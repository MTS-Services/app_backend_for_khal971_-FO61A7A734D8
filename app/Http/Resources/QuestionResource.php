<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'question_details_id' => $this->questionDetails->id,
            'status'      => $this->status_label,
            'statusList'  => $this->status_list,
            'language'    => translation($this->translations)->language ?? 'en',
            'title'       => translation($this->translations)->title ?? 'Not Found',
            'answer'      => translation($this->translations)->answer ?? 'Not Found',
            'created_at'  => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at'  => $this->updated_at_formatted ?? "N/A",
            'created_by'  => $this->creater?->name ?? "System",
            'updated_by'  => $this->updater?->name ?? "N/A",
        ];
        $relations = ['question_details' => new QuestionDetailResource($this->whenLoaded('questionDetails'), 'lite')];
        $relations['user'] = new UserResource($this->whenLoaded('user'));

        return match ($this->type) {
            'lite' => $lite,
            default => array_merge($lite, $relations),
        };
    }
}
