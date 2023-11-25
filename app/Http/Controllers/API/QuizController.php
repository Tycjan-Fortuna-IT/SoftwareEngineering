<?php

namespace App\Http\Controllers\API;

use App\Helpers\Managers\UserLevelManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\QuizResource;
use App\Models\Question;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class QuizController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Quiz $quiz
     * @return JsonResponse
     */
    public function update(Request $request, Quiz $quiz): JsonResponse
    {
        $request->validate([
            'questions' => 'required|array|min:5|max:5',
            'questions.*.uuid' => 'required|uuid|exists:questions,uuid',
            'questions.*.answer' => 'required|integer|between:0,4',
        ]);

        $questions = $quiz->questions()->get();

        $response = [];
        $response['result'] = [];

        $result = 0;
        $exp = 0;

        $questionsByUuid = array_column($request->questions, null, 'uuid');

        foreach ($questions as $question) {
            if (isset($questionsByUuid[$question->uuid])) {
                $q = $questionsByUuid[$question->uuid];
                $isCorrect = $question->correct == $q["answer"];

                if ($isCorrect) {
                    $result++;
                    $exp += $question->prize;
                }

                $response['result'][] = [
                    'uuid' => $question->uuid,
                    'correct_answer' => $question->correct,
                    'user_answer' => $q["answer"],
                    'is_correct' => $isCorrect
                ];

                $quiz->questions()->updateExistingPivot($question->id, [
                    'answer' => $q["answer"],
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        UserLevelManager::AddExp(Auth::user(), $exp);

        $response['info'] = [
            'correct_answers' => $result,
            'result' => $result / $questions->count() * 100,
            'experience' => $exp,
        ];

        $quiz->result = $response['info']['result'];
        $quiz->save();

        return response()->json([
            'data' => $response
        ]);
    }

    /**
     * Get random quiz for the user. Composed of 5 random questions.
     *
     * @param Request $request
     * @return QuizResource
     */
    public function getRandom(Request $request): QuizResource
    {
        $questions = Question::inRandomOrder()->limit(5)->get();

        $quiz = Quiz::create([
            'user_id' => Auth::id(),
            'result' => -1,
        ]);

        $quiz->questions()->attach($questions, [
            'answer' => -1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return new QuizResource($quiz);
    }
}
