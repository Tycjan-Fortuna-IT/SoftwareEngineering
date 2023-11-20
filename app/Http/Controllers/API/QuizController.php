<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\QuizResource;
use App\Models\Question;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection|JsonResponse
     */
    public function index(Request $request): ResourceCollection|JsonResponse
    {
        if (!$request->has('filter.user_uuid')) {
            return response()->json(['message' => 'Missing filter[user_uuid], it is required.'], 400);
        }

        $quizzes = QueryBuilder::for(Quiz::class)
            ->allowedFilters([
                AllowedFilter::scope('user_uuid'),
            ])
            ->where('created_at', '>=', now()->subDay())
            ->get();

        return QuizResource::collection($quizzes);
    }

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
            'questions.*.answer' => 'required|integer|between:1,4',
        ]);

        $questions = $quiz->questions()->get();

        $response = [];
        $response['result'] = [];

        $result = 0;

        foreach ($questions as $question) {
            foreach ($request->questions as $q) {

                if ($question->uuid == $q["uuid"]) {
                    $isCorrect = $question->pivot->answer == $q["answer"] ? true : false;

                    if ($isCorrect) $result++; // TODO: ADD POINTS to the user, needs to be heavily tested ASAP

                    $response['result'][] = [
                        'uuid' => $question->uuid,
                        'correct_answer' => $question->pivot->answer,
                        'user_answer' => $q["answer"],
                        'is_correct' => $isCorrect
                    ];
                }

            }
        }

        $response['info'] = [
            'correct_answers' => $result,
            'result' => $result / $questions->count() * 100
        ];

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
