<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private $type;
    public function __construct($resource, $type = 'quiz_answer')
    {
        parent::__construct($resource);
        $this->type = $type;
    }
    public function toArray(Request $request): array
    {
        $lite = [
            'id' => $this->id,
            'order_index' => $this->order_index,
            'quiz_id' => $this->quiz_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];
        $relations = ['quiz' => new QuizResource($this->whenLoaded('quiz'))];
        $relations['user'] = new UserResource($this->whenLoaded('user'));
        return match ($this->type) {
            'lite' => $lite,
            default => array_merge($lite, $relations),
        };
    }
}

// {
//     "message": "Quiz Answer Fetched Successfully",
//     "data": {
//         "id": 1,
//         "order_index": 0,
//         "quiz_id": 1,
//         "user_id": 1,
//         "created_at": null,
//         "updated_at": null,
//         "created_by": null,
//         "updated_by": null,
//         "quiz": {
//             "id": 1,
//             "order_index": 0,
//             "topic_id": 2,
//             "status": 1,
//             "created_at": null,
//             "updated_at": null,
//             "created_by": null,
//             "updated_by": null,
//             "status_label": "Active",
//             "topics": {
//                 "id": 2,
//                 "order_index": 0,
//                 "course_id": 1,
//                 "status": 1,
//                 "created_at": null,
//                 "updated_at": null,
//                 "created_by": null,
//                 "updated_by": null,
//                 "status_label": "Active",
//                 "translations": [
//                     {
//                         "topic_id": 2,
//                         "language": "en",
//                         "name": "Linear Equations"
//                     }
//                 ],
//                 "course": {
//                     "id": 1,
//                     "order_index": 0,
//                     "subject_id": 1,
//                     "status": 1,
//                     "created_at": null,
//                     "updated_at": null,
//                     "created_by": null,
//                     "updated_by": null,
//                     "status_label": "Active",
//                     "translations": [
//                         {
//                             "course_id": 1,
//                             "language": "en",
//                             "name": "Algebra Basics"
//                         }
//                     ],
//                     "subject": {
//                         "id": 1,
//                         "order_index": 1,
//                         "icon": null,
//                         "status": 1,
//                         "created_at": null,
//                         "updated_at": null,
//                         "created_by": null,
//                         "updated_by": null,
//                         "status_label": "Active",
//                         "translations": [
//                             {
//                                 "subject_id": 1,
//                                 "language": "ar",
//                                 "name": "اللغة الإنجليزية"
//                             },
//                             {
//                                 "subject_id": 1,
//                                 "language": "en",
//                                 "name": "English"
//                             },
//                             {
//                                 "subject_id": 1,
//                                 "language": "es",
//                                 "name": "Inglés"
//                             },
//                             {
//                                 "subject_id": 1,
//                                 "language": "it",
//                                 "name": "Inglese"
//                             }
//                         ]
//                     }
//                 }
//             }
//         },
//         "user": {
//             "id": 1,
//             "order_index": 0,
//             "username": null,
//             "name": "Admin",
//             "phone": "1234567890",
//             "email": "admin@dev.com",
//             "user_class_id": null,
//             "image": null,
//             "dob": null,
//             "gender": null,
//             "country": null,
//             "city": null,
//             "school": null,
//             "is_premium": false,
//             "premium_expires_at": null,
//             "email_verified_at": "2025-07-09T12:39:55.000000Z",
//             "otp": null,
//             "otp_sent_at": null,
//             "otp_expires_at": null,
//             "is_admin": true,
//             "status": 1,
//             "created_at": "2025-07-09T12:39:55.000000Z",
//             "updated_at": "2025-07-09T12:39:55.000000Z",
//             "created_by": null,
//             "updated_by": null,
//             "status_label": "Active",
//             "status_list": {
//                 "1": "Active",
//                 "0": "Inactive"
//             },
//             "gender_label": "Unknown",
//             "gender_list": {
//                 "1": "Male",
//                 "2": "Female",
//                 "3": "Other"
//             }
//         }
//     }
// }
