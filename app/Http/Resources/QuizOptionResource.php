<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lite = [
            'id'                => $this->id,
            'order_index'       => $this->order_index,
            'quiz_id'           => $this->quiz_id,
            'is_correct'        => $this->is_correct_label,
            'is_correctList'    => $this->is_correct_list,
            'language'          => translation($this->translations)->language ?? 'en',
            'title'             => translation($this->translations)->title ?? 'Not Found',
            'created_at'        => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at'        => $this->updated_at_formatted ?? "N/A",
            'created_by'        => $this->creater?->name ?? "System",
            'updated_by'        => $this->updater?->name ?? "N/A",
        ];

        $relations = ['quiz' => new QuizResource($this->whenLoaded('quiz'), 'lite')];
        return match ($this->type) {
            'lite' => $lite,
            default => array_merge($lite, $relations),
        };
    }
}
