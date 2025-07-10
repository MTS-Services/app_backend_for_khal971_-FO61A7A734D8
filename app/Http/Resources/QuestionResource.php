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





//         {
//     "success": true,
//     "message": "Question list fetched successfully",
//     "data": [
//         {
//             "id": 2,
//             "order_index": 0,
//             "question_details_id": 2,
//             "status": 1,
//             "created_at": null,
//             "updated_at": null,
//             "created_by": null,
//             "updated_by": null,
//             "status_label": "Active",
//             "translations": [
//                 {
//                     "question_id": 2,
//                     "language": "en",
//                     "title": "Who was the first President of the United States?",
//                     "answer": "George Washington was the first U.S. President."
//                 }
//             ],
//             "question_details": {
//                 "id": 2,
//                 "order_index": 0,
//                 "topic_id": 2,
//                 "file": null,
//                 "status": 1,
//                 "created_at": null,
//                 "updated_at": null,
//                 "created_by": null,
//                 "updated_by": null,
//                 "translations": [
//                     {
//                         "question_detail_id": 2,
//                         "language": "en",
//                         "title": "Explain the water cycle.",
//                         "description": "Briefly describe the stages."
//                     }
//                 ],
//                 "topic": {
//                     "id": 2,
//                     "order_index": 0,
//                     "course_id": 1,
//                     "status": 1,
//                     "created_at": null,
//                     "updated_at": null,
//                     "created_by": null,
//                     "updated_by": null,
//                     "status_label": "Active",
//                     "translations": [
//                         {
//                             "topic_id": 2,
//                             "language": "en",
//                             "name": "Linear Equations"
//                         }
//                     ]
//                 }
//             }
//         }
//     ]
// }
    }
