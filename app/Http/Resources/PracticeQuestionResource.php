<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PracticeQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [

            'id' => $this->id ?? 'Not Found',
            'status' => $this->status_label ?? 'Not Found',
            'attempts' => $this->attempts ?? 'Not Found',
            'statusList' => $this->status_list ?? 'Not Found',
        ];
    }
}
