<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{

    private $type;
    public function __construct($resource, $type = 'question')
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
            'status' => $this->status_label,
            'statusList' => $this->status_list,
            'language' => translation($this->translations)->language ?? 'Not Found',
            'title' => translation($this->translations)->title ?? 'Not Found',
            'answer' => translation($this->translations)->answer ?? 'Not Found',
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
        ];
        $audits = [
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];
        $relations['question_details'] = new QuestionDetailResource($this->whenLoaded('questionDetails'), 'lite');
        $relations['user'] = new UserResource($this->whenLoaded('user'));

        return match ($this->type) {
            'lite' => $lite,
            default => array_merge($lite, $audits, $relations),
        };
    }
}
