<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Topic;
use App\Models\Question;
use App\Models\UserProgress; // Refers to the user_progress table [cite: 1334]
use App\Models\UserItemProgress; // Refers to the user_item_progress table
use App\Models\UserItemProgresss;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgressControllerTest extends Controller
{
    /**
     * Get single question progress
     * GET /api/progress/question/{userId}/{questionId}
     * (Assuming this method is already converted as per the provided context)
     */
    public function getQuestionProgress(int $userId, int $questionId): JsonResponse
    {
        try {
            $progress = UserItemProgresss::with([
                'question.questionDetails', // Eager load question and its details
                'question.questionDetails.topic.course.subject' // Eager load topic, course, and subject
            ])
            ->where('user_id', $userId)
            ->where('item_type', 'question')
            ->where('item_id', $questionId)
            ->first();

            if (!$progress) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_id' => $userId,
                        'question_id' => $questionId,
                        'status' => 'not_started',
                        'attempts' => 0,
                        'correct_attempts' => 0,
                        'time_spent' => 0,
                        'score' => null,
                        'progress_percentage' => 0,
                        'success_rate' => 0,
                        'is_bookmarked' => false,
                        'is_flagged' => false
                    ],
                    'message' => 'No progress found, showing default state'
                ]);
            }

            $data = [
                'id' => $progress->id,
                'user_id' => $progress->user_id,
                'question_id' => $progress->item_id,
                'status' => $progress->status,
                'attempts' => $progress->attempts,
                'correct_attempts' => $progress->correct_attempts,
                'time_spent' => $progress->time_spent,
                'score' => $progress->score,
                'is_bookmarked' => $progress->is_bookmarked,
                'is_flagged' => $progress->is_flagged,
                'notes' => $progress->notes,
                'first_accessed_at' => $progress->first_accessed_at,
                'last_accessed_at' => $progress->last_accessed_at,
                'completed_at' => $progress->completed_at,
                'question_title' => $progress->question?->title,
                'question_description' => $progress->question?->questionDetail?->title,
                'question_file' => $progress->question?->questionDetail?->file,
                'progress_percentage' => $progress->progress_percentage, // Assuming this is an accessor on the model
                'success_rate' => $progress->success_rate // Assuming this is an accessor on the model
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching question progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching question progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get topic progress
     * GET /api/progress/topic/{userId}/{topicId}
     * (Assuming this method is already converted as per the provided context)
     */
    public function getTopicProgress(int $userId, int $topicId): JsonResponse
    {
        try {
            $progress = UserProgress::with([
                'topic.course.subject' // Eager load topic, course, and subject
            ])
            ->where('user_id', $userId)
            ->where('content_type', 'topic')
            ->where('content_id', $topicId)
            ->first();

            if (!$progress) {
                $topic = Topic::with(['course.subject', 'questionDetails.questions'])->find($topicId);
                $totalQuestions = $topic?->questionDetails()->withCount('questions')->get()->sum('questions_count') ?? 0;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_id' => $userId,
                        'topic_id' => $topicId,
                        'total_questions' => $totalQuestions,
                        'completed_questions' => 0,
                        'correct_questions' => 0,
                        'completion_percentage' => 0,
                        'accuracy_percentage' => 0,
                        'status' => 'not_started',
                        'progress_status_text' => 'Not Started',
                        'topic_name' => $topic?->name ?? 'Unknown Topic',
                        'course_name' => $topic?->course?->name ?? 'Unknown Course',
                        'subject_name' => $topic?->course?->subject?->name ?? 'Unknown Subject',
                        'remaining_questions' => $totalQuestions,
                    ],
                    'message' => 'No progress found, showing default state'
                ]);
            }

            $data = [
                'id' => $progress->id,
                'user_id' => $progress->user_id,
                'topic_id' => $progress->content_id,
                'total_questions' => $progress->total_items,
                'completed_questions' => $progress->completed_items,
                'correct_questions' => $progress->correct_items,
                'completion_percentage' => $progress->completion_percentage,
                'accuracy_percentage' => $progress->accuracy_percentage,
                'total_time_spent' => $progress->total_time_spent,
                'average_time_per_item' => $progress->average_time_per_item,
                'status' => $progress->status,
                'current_streak' => $progress->current_streak,
                'first_accessed_at' => $progress->first_accessed_at,
                'last_accessed_at' => $progress->last_accessed_at,
                'completed_at' => $progress->completed_at,
                'topic_name' => $progress->topic?->name,
                'course_name' => $progress->topic?->course?->name,
                'subject_name' => $progress->topic?->course?->subject?->name,
                'remaining_questions' => $progress->total_items - $progress->completed_items,
                'progress_status_text' => $this->getCompletionStatusText($progress->completion_percentage), // Helper method
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching topic progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching topic progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get completion status text
     */
    private function getCompletionStatusText(float $completionPercentage): string
    {
        if ($completionPercentage == 100) {
            return 'Completed';
        } elseif ($completionPercentage >= 75) {
            return 'Almost Done';
        } elseif ($completionPercentage >= 50) {
            return 'Half Way';
        } elseif ($completionPercentage >= 25) {
            return 'Getting Started';
        } elseif ($completionPercentage > 0) {
            return 'Just Started';
        } else {
            return 'Not Started';
        }
    }

    /**
     * GET ALL QUESTIONS PROGRESS IN TOPIC
     * GET /api/progress/topic/{userId}/{topicId}/questions
     */
    public function getTopicQuestionsProgress(int $userId, int $topicId): JsonResponse
    {
        try {
            // Find the UserProgress for the topic
            $topicProgress = UserProgress::where('user_id', $userId)
                ->where('content_type', 'topic')
                ->where('content_id', $topicId)
                ->first();

            if (!$topicProgress) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'message' => 'No progress found for this topic.'
                ]);
            }

            $questionsProgress = UserItemProgresss::with('question.questionDetail')
                ->where('parent_progress_id', $topicProgress->id)
                ->orderBy('item_order')
                ->get()
                ->map(function ($item) use ($topicProgress) {
                    // This logic mirrors the old SQL CASE statement for progress_icon and is_next_question
                    $progressIcon = '';
                    switch ($item->status) {
                        case 'correct':
                            $progressIcon = ''; // Assuming checkmark or similar
                            break;
                        case 'incorrect':
                            $progressIcon = 'X';
                            break;
                        case 'attempted':
                            $progressIcon = 'A';
                            break;
                        case 'viewed':
                            $progressIcon = 'V'; // Added 'V' for viewed, based on snippet
                            break;
                        default:
                            $progressIcon = 'O'; // For 'not started' or other statuses
                            break;
                    }

                    // Determine the next question to study in the topic
                    $isNextQuestion = false;
                    $minNotStartedOrViewedOrder = UserItemProgresss::where('parent_progress_id', $topicProgress->id)
                                                    ->whereIn('status', ['not_started', 'viewed'])
                                                    ->min('item_order');

                    if ($item->item_order == $minNotStartedOrViewedOrder && in_array($item->status, ['not_started', 'viewed'])) {
                        $isNextQuestion = true;
                    }


                    return [
                        'question_id' => $item->item_id,
                        'status' => $item->status,
                        'attempts' => $item->attempts,
                        'correct_attempts' => $item->correct_attempts,
                        'time_spent' => $item->time_spent,
                        'score' => $item->score,
                        'last_accessed_at' => $item->last_accessed_at,
                        'item_order' => $item->item_order,
                        'question_title' => $item->question?->title,
                        'question_description' => $item->question?->questionDetail?->description,
                        'progress_icon' => $progressIcon,
                        'is_next_question' => $isNextQuestion,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $questionsProgress,
                'total' => $questionsProgress->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching topic questions progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching topic questions progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPDATE INDIVIDUAL ITEM PROGRESS
     * POST /api/progress/item/update
     * Request body: { userId, itemType, itemId, status, isCorrect, timeSpent, score, notes }
     */
    public function updateItemProgress(Request $request): JsonResponse
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'itemType' => 'required|string|in:question,topic,course,subject,quiz,question_set', // Based on content_type enum
            'itemId' => 'required|integer',
            'status' => 'required|string|in:not_started,in_progress,completed,mastered,viewed,attempted,incorrect,correct', // Extend as per your actual statuses
            'isCorrect' => 'boolean|nullable',
            'timeSpent' => 'integer|nullable',
            'score' => 'numeric|nullable',
            'notes' => 'string|nullable',
        ]);

        $userId = $request->input('userId');
        $itemType = $request->input('itemType');
        $itemId = $request->input('itemId');
        $status = $request->input('status');
        $isCorrect = $request->input('isCorrect', false);
        $timeSpent = $request->input('timeSpent', 0);
        $score = $request->input('score');
        $notes = $request->input('notes');

        DB::beginTransaction();

        try {
            $progress = UserItemProgresss::where('user_id', $userId)
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->first();

            if (!$progress) {
                // Create new progress record
                $progress = new UserItemProgresss();
                $progress->user_id = $userId;
                $progress->item_type = $itemType;
                $progress->item_id = $itemId;
                $progress->status = $status;
                $progress->attempts = 1;
                $progress->correct_attempts = $isCorrect ? 1 : 0;
                $progress->time_spent = $timeSpent;
                $progress->score = $score;
                $progress->notes = $notes;
                $progress->first_accessed_at = now();
                $progress->last_accessed_at = now();
                $progress->completed_at = ($status === 'completed' || $status === 'correct') ? now() : null;
                $progress->created_at = now();
                // Assuming item_order and parent_progress_id are handled elsewhere or can be null initially
                // For a question, you might need to link it to a topic's user_progress record for parent_progress_id
                // This would require fetching the parent progress based on itemType's hierarchy
                $progress->save();
            } else {
                // Update existing progress
                $progress->status = $status;
                $progress->attempts += 1;
                $progress->correct_attempts += $isCorrect ? 1 : 0;
                $progress->time_spent += $timeSpent;
                $progress->score = $score ?? $progress->score; // COALESCE equivalent
                $progress->notes = $notes ?? $progress->notes; // COALESCE equivalent
                $progress->last_accessed_at = now();
                if (($status === 'completed' || $status === 'correct') && is_null($progress->completed_at)) {
                    $progress->completed_at = now();
                }
                $progress->updated_at = now();
                $progress->save();
            }

            // After updating user_item_progress, you typically need to recalculate
            // parent progress (topic, course, subject).
            // This would involve a separate helper or job that aggregates child progress.
            // For simplicity, we'll assume a method `updateParentProgress` exists or will be implemented.
            // $this->updateParentProgress($userId, $itemType, $itemId); // This would be a more complex recursive update

            DB::commit();

            // Fetch the updated progress to return
            $updatedProgress = UserItemProgresss::with('question.questionDetail')->find($progress->id);

            return response()->json([
                'success' => true,
                'data' => $updatedProgress,
                'message' => 'Progress updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET NEXT ITEM TO STUDY
     * GET /api/progress/next/{userId}
     */
    public function getNextItemToStudy(int $userId): JsonResponse
    {
        try {
            // Find the next item that is 'not_started' or 'viewed' for the user
            // Prioritize by most recently accessed topic, then by item_order within that topic
            $nextItem = UserItemProgresss::where('user_id', $userId)
                ->whereIn('status', ['not_started', 'viewed'])
                ->with([
                    'question.questionDetail', // Load question and its details
                    'parentProgress' => function ($query) { // Load parent progress (e.g., topic progress)
                        $query->with(['topic.course.subject']); // Load topic, course, subject details
                    }
                ])
                ->orderByRaw('CASE WHEN status = "not_started" THEN 1 ELSE 2 END') // Prioritize not_started
                ->orderBy('last_accessed_at', 'desc') // Order by parent topic's last accessed at (or item's)
                ->orderBy('item_order', 'asc')
                ->first();

            if (!$nextItem) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No more items to study'
                ]);
            }

            // Manually calculate completed_before_this and total_in_topic if needed for the response structure
            $completedBeforeThis = 0;
            $totalInTopic = 0;
            if ($nextItem->parentProgress) {
                $completedBeforeThis = UserItemProgresss::where('parent_progress_id', $nextItem->parent_progress_id)
                                        ->whereIn('status', ['completed', 'correct'])
                                        ->count();
                $totalInTopic = $nextItem->parentProgress->total_items;
            }

            $data = [
                'item_type' => $nextItem->item_type,
                'item_id' => $nextItem->item_id,
                'item_order' => $nextItem->item_order,
                'item_title' => $nextItem->question?->title,
                'item_description' => $nextItem->question?->questionDetail?->description,
                'topic_name' => $nextItem->parentProgress?->topic?->name,
                'course_name' => $nextItem->parentProgress?->topic?->course?->name,
                'subject_name' => $nextItem->parentProgress?->topic?->course?->subject?->name,
                'topic_progress' => $nextItem->parentProgress?->completion_percentage,
                'completed_before_this' => $completedBeforeThis,
                'total_in_topic' => $totalInTopic,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching next item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching next item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET PROGRESS SUMMARY FOR MULTIPLE ITEMS (BATCH)
     * POST /api/progress/batch
     * Request body: { userId, items: [{type: 'question', id: 1}, {type: 'topic', id: 2}] }
     */
    public function getBatchProgress(Request $request): JsonResponse
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'items' => 'required|array',
            'items.*.type' => 'required|string|in:question,topic', // Assuming only question and topic for batch
            'items.*.id' => 'required|integer',
        ]);

        $userId = $request->input('userId');
        $items = $request->input('items');
        $results = [];

        try {
            foreach ($items as $item) {
                if ($item['type'] === 'question') {
                    $progress = UserItemProgresss::where('user_id', $userId)
                        ->where('item_type', 'question')
                        ->where('item_id', $item['id'])
                        ->first();

                    $results[] = [
                        'item_type' => 'question',
                        'item_id' => $item['id'],
                        'status' => $progress->status ?? 'not_started',
                        'attempts' => $progress->attempts ?? 0,
                        'correct_attempts' => $progress->correct_attempts ?? 0,
                        'time_spent' => $progress->time_spent ?? 0,
                        'score' => $progress->score ?? 0,
                        'progress_percentage' => $progress->progress_percentage ?? 0,
                    ];
                } elseif ($item['type'] === 'topic') {
                    $progress = UserProgress::where('user_id', $userId)
                        ->where('content_type', 'topic')
                        ->where('content_id', $item['id'])
                        ->first();

                    $results[] = [
                        'item_type' => 'topic',
                        'item_id' => $item['id'],
                        'status' => $progress->status ?? 'not_started',
                        'total_time_spent' => $progress->total_time_spent ?? 0,
                        'completion_percentage' => $progress->completion_percentage ?? 0,
                        'accuracy_percentage' => $progress->accuracy_percentage ?? 0,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => count($results)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching batch progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching batch progress',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * BOOKMARK/UNBOOKMARK ITEM
     * POST /api/progress/bookmark
     * Request body: { userId, itemType, itemId, isBookmarked }
     */
    public function toggleBookmark(Request $request): JsonResponse
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'itemType' => 'required|string|in:question,topic,course,subject,quiz,question_set',
            'itemId' => 'required|integer',
            'isBookmarked' => 'required|boolean',
        ]);

        $userId = $request->input('userId');
        $itemType = $request->input('itemType');
        $itemId = $request->input('itemId');
        $isBookmarked = $request->input('isBookmarked');

        try {
            $progress = UserItemProgresss::where('user_id', $userId)
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->first();

            if (!$progress) {
                // Create new progress record if it doesn't exist, set status to 'viewed'
                UserItemProgresss::create([
                    'user_id' => $userId,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'is_bookmarked' => $isBookmarked,
                    'status' => 'viewed', // Default status when creating for bookmark
                    'created_at' => now(),
                    'last_accessed_at' => now(),
                ]);
            } else {
                // Update existing record
                $progress->is_bookmarked = $isBookmarked;
                $progress->updated_at = now();
                $progress->save();
            }

            $message = $isBookmarked ? 'Item bookmarked successfully' : 'Bookmark removed successfully';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating bookmark: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating bookmark',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * FLAG/UNFLAG ITEM
     * POST /api/progress/flag
     * Request body: { userId, itemType, itemId, isFlagged, flagReason }
     */
    public function toggleFlag(Request $request): JsonResponse
    {
        $request->validate([
            'userId' => 'required|integer|exists:users,id',
            'itemType' => 'required|string|in:question,topic,course,subject,quiz,question_set',
            'itemId' => 'required|integer',
            'isFlagged' => 'required|boolean',
            'flagReason' => 'string|nullable',
        ]);

        $userId = $request->input('userId');
        $itemType = $request->input('itemType');
        $itemId = $request->input('itemId');
        $isFlagged = $request->input('isFlagged');
        $flagReason = $request->input('flagReason');

        try {
            $progress = UserItemProgresss::where('user_id', $userId)
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->first();

            if (!$progress) {
                // Create new progress record if it doesn't exist, set status to 'viewed'
                UserItemProgresss::create([
                    'user_id' => $userId,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'is_flagged' => $isFlagged,
                    'notes' => $flagReason, // Using notes for flagReason
                    'status' => 'viewed', // Default status when creating for flag
                    'created_at' => now(),
                    'last_accessed_at' => now(),
                ]);
            } else {
                // Update existing record
                $progress->is_flagged = $isFlagged;
                $progress->notes = $isFlagged ? ($flagReason ?? $progress->notes) : null; // Clear notes if unflagging
                $progress->updated_at = now();
                $progress->save();
            }

            $message = $isFlagged ? 'Item flagged successfully' : 'Flag removed successfully';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating flag: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating flag',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}