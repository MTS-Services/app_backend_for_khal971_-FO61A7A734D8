<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PracticeTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'last_practice_date' => $this->updated_at_formatted ? $this->updated_at_formatted : ($this->created_at_formatted ?? 'N/A'),
            'topicDetails' => new TopicResource($this->whenLoaded('practiceable'), 'practice'),
        ];
    }
}
