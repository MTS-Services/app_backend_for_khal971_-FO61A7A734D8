<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BatchProgressRequest;
use App\Http\Requests\Api\BookmarkRequest;
use App\Http\Requests\Api\FlagRequest;
use App\Http\Requests\Api\UpdateItemProgressRequest;
use App\Models\Topic;
use App\Models\UserItemProgresss;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgressController extends Controller
{

    /**
     * Get single question progress
     * GET /api/v1/progress/question/{userId}/{questionId}
     */
    public function getQuestionProgress(int $userId, int $questionId): JsonResponse
    {
        try {
            $progress = UserItemProgresss::with([
                'question.questionDetails',
                'question.questionDetails.topic.course.subject'
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
                'progress_percentage' => $progress->progress_percentage,
                'success_rate' => $progress->success_rate
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
     * Get progress summary for single topic
     * GET /api/v1/progress/topic/{userId}/{topicId}
     *
     * @param int $userId
     * @param int $topicId
     * @return JsonResponse
     */
    public function getTopicProgress(int $userId, int $topicId): JsonResponse
    {
        try {
            $progress = UserProgress::with([
                'topic.course.subject'
            ])
                ->where('user_id', $userId)
                ->where('content_type', 'topic')
                ->where('content_id', $topicId)
                ->first();

            if (!$progress) {
                $topic = Topic::with(['course.subject', 'questionDetails.questions'])
                    ->find($topicId);

                $totalQuestions = $topic?->questionDetails()
                    ->withCount('questions')
                    ->get()
                    ->sum('questions_count') ?? 0;

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
                        'remaining_questions' => $totalQuestions
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
                'status' => $progress->status,
                'current_streak' => $progress->current_streak,
                'first_accessed_at' => $progress->first_accessed_at,
                'last_accessed_at' => $progress->last_accessed_at,
                'completed_at' => $progress->completed_at,
                'topic_name' => $progress->topic?->name,
                'course_name' => $progress->topic?->course?->name,
                'subject_name' => $progress->topic?->course?->subject?->name,
                'remaining_questions' => $progress->remaining_questions,
                'progress_status_text' => $this->getCompletionStatusText($progress->completion_percentage),
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
     * Get all questions progress in topic
     * GET /api/v1/progress/topic/{userId}/{topicId}/questions
     */
    public function getTopicQuestionsProgress(int $userId, int $topicId): JsonResponse
    {
        try {
            $parentProgress = UserProgress::where('user_id', $userId)
                ->where('content_type', 'topic')
                ->where('content_id', $topicId)
                ->first();

            if (!$parentProgress) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'total' => 0
                ]);
            }

            $questions = UserItemProgresss::with([
                'question.questionDetail'
            ])
                ->where('parent_progress_id', $parentProgress->id)
                ->orderBy('item_order')
                ->get();

            $nextQuestionOrder = $questions->where('status', 'not_started')
                ->orWhere('status', 'viewed')
                ->min('item_order');

            $data = $questions->map(function ($question) use ($nextQuestionOrder) {
                return [
                    'question_id' => $question->item_id,
                    'status' => $question->status,
                    'attempts' => $question->attempts,
                    'correct_attempts' => $question->correct_attempts,
                    'time_spent' => $question->time_spent,
                    'score' => $question->score,
                    'last_accessed_at' => $question->last_accessed_at,
                    'item_order' => $question->item_order,
                    'question_title' => $question->question?->title,
                    'question_description' => $question->question?->questionDetail?->description,
                    'progress_icon' => $question->progress_icon,
                    'is_next_question' => $question->item_order === $nextQuestionOrder
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $data->count()
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
     * POST /api/v1/progress/item/update
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
            // You can implement this logic here directly, or create a separate service/job.
            // For now, the explicit call to an undefined method is removed.

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
     * Get next item to study
     * GET /api/v1/progress/next/{userId}
     */
    public function getNextItemToStudy(int $userId): JsonResponse
    {
        try {
            $nextItem = UserItemProgresss::with([
                'parentProgress.topic.course.subject',
                'question.questionDetail'
            ])
                ->whereHas('parentProgress', function ($query) {
                    $query->where('content_type', 'topic');
                })
                ->where('user_id', $userId)
                ->whereIn('status', ['not_started', 'viewed'])
                ->orderBy('last_accessed_at', 'desc')
                ->orderBy('item_order', 'asc')
                ->first();

            if (!$nextItem) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No more items to study'
                ]);
            }

            $completedBefore = UserItemProgresss::where('parent_progress_id', $nextItem->parent_progress_id)
                ->whereIn('status', ['completed', 'correct'])
                ->count();

            $data = [
                'item_type' => 'question',
                'item_id' => $nextItem->item_id,
                'item_order' => $nextItem->item_order,
                'item_title' => $nextItem->question?->title,
                'item_description' => $nextItem->question?->questionDetail?->description,
                'topic_name' => $nextItem->parentProgress?->topic?->name,
                'course_name' => $nextItem->parentProgress?->topic?->course?->name,
                'subject_name' => $nextItem->parentProgress?->topic?->course?->subject?->name,
                'topic_progress' => $nextItem->parentProgress?->completion_percentage,
                'completed_before_this' => $completedBefore,
                'total_in_topic' => $nextItem->parentProgress?->total_items
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
     * Get progress summary for multiple items
     * POST /api/v1/progress/batch
     */
    public function getBatchProgress(BatchProgressRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $results = [];

            foreach ($validated['items'] as $item) {
                if ($item['type'] === 'question') {
                    $progress = UserItemProgresss::where('user_id', $validated['user_id'])
                        ->where('item_type', $item['type'])
                        ->where('item_id', $item['id'])
                        ->first();

                    $results[] = [
                        'item_type' => $item['type'],
                        'item_id' => $item['id'],
                        'status' => $progress?->status ?? 'not_started',
                        'attempts' => $progress?->attempts ?? 0,
                        'correct_attempts' => $progress?->correct_attempts ?? 0,
                        'time_spent' => $progress?->time_spent ?? 0,
                        'score' => $progress?->score ?? 0,
                        'progress_percentage' => $progress?->progress_percentage ?? 0
                    ];
                } elseif ($item['type'] === 'topic') {
                    $progress = UserProgress::where('user_id', $validated['user_id'])
                        ->where('content_type', $item['type'])
                        ->where('content_id', $item['id'])
                        ->first();

                    $results[] = [
                        'item_type' => $item['type'],
                        'item_id' => $item['id'],
                        'status' => $progress?->status ?? 'not_started',
                        'completion_percentage' => $progress?->completion_percentage ?? 0,
                        'accuracy_percentage' => $progress?->accuracy_percentage ?? 0,
                        'total_items' => $progress?->total_items ?? 0,
                        'completed_items' => $progress?->completed_items ?? 0,
                        'total_time_spent' => $progress?->total_time_spent ?? 0
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
     * Toggle bookmark
     * POST /api/v1/progress/bookmark
     */
    public function toggleBookmark(BookmarkRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $progress = UserItemProgresss::where('user_id', $validated['user_id'])
                ->where('item_type', $validated['item_type'])
                ->where('item_id', $validated['item_id'])
                ->first();

            if (!$progress) {
                UserItemProgresss::create([
                    'user_id' => $validated['user_id'],
                    'item_type' => $validated['item_type'],
                    'item_id' => $validated['item_id'],
                    'is_bookmarked' => $validated['is_bookmarked'],
                    'status' => 'viewed'
                ]);
            } else {
                $progress->update([
                    'is_bookmarked' => $validated['is_bookmarked']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $validated['is_bookmarked'] ? 'Item bookmarked successfully' : 'Bookmark removed successfully'
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
     * Toggle flag
     * POST /api/v1/progress/flag
     */
    public function toggleFlag(FlagRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $progress = UserItemProgresss::where('user_id', $validated['user_id'])
                ->where('item_type', $validated['item_type'])
                ->where('item_id', $validated['item_id'])
                ->first();

            if (!$progress) {
                UserItemProgresss::create([
                    'user_id' => $validated['user_id'],
                    'item_type' => $validated['item_type'],
                    'item_id' => $validated['item_id'],
                    'is_flagged' => $validated['is_flagged'],
                    'notes' => $validated['flag_reason'] ?? null,
                    'status' => 'viewed'
                ]);
            } else {
                $updateNotes = $validated['is_flagged']
                    ? ($progress->notes ? $progress->notes . "\n[FLAG]: " . $validated['flag_reason'] : "[FLAG]: " . $validated['flag_reason'])
                    : $progress->notes;

                $progress->update([
                    'is_flagged' => $validated['is_flagged'],
                    'notes' => $updateNotes
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $validated['is_flagged'] ? 'Item flagged successfully' : 'Flag removed successfully'
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
