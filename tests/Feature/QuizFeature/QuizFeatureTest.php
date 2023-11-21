<?php

namespace Tests\Feature\QuizFeature;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class QuizFeatureTest extends APIUnitTestCase
{
    public function test_solving_randomly_generated_quiz_with_some_correct_answers_yields_exp()
    {
        $user = $this->prepare_user();

        $user->level = 0;
        $user->experience = 0;

        $user->save();
        $user->refresh();

        $this->assertEquals(0, $user->level);
        $this->assertEquals(0, $user->experience);

        $quiz = $this->actingAs($user)
            ->getJson('/api/quizzes/getRandom')
            ->assertStatus(Response::HTTP_CREATED)
            ->json()['data'];

        $this->assertDatabaseHas('quizzes', [
            'uuid' => $quiz['uuid'],
            'user_id' => $user->id,
            'result' => -1,
        ]);

        $questions = $quiz['questions'];

        $this->assertEquals(5, count($questions));

        $question2 = $questions[1];
        $question4 = $questions[3];

        $result = $this->putJson('/api/quizzes/' . $quiz['uuid'] , [
            'questions' => [
                [ 'uuid' => $questions[0]['uuid'], 'answer' => 0 ],
                [ 'uuid' => $questions[1]['uuid'], 'answer' => $question2['correct'] ],
                [ 'uuid' => $questions[2]['uuid'], 'answer' => 0 ],
                [ 'uuid' => $questions[3]['uuid'], 'answer' => $question4['correct'] ],
                [ 'uuid' => $questions[4]['uuid'], 'answer' => 0 ],
            ],
        ])->assertStatus(Response::HTTP_OK)
            ->json();

        $user->refresh();

        $this->assertEquals(0, $user->level);
        $this->assertEquals($question2['prize'] + $question4['prize'], $user->experience);
        $this->assertEquals(2, $result['data']['info']['correct_answers']);
        $this->assertEquals(40, $result['data']['info']['result']);
        $this->assertEquals($question2['prize'] + $question4['prize'], $result['data']['info']['experience']);

        $checkedQuestions = $result['data']['result'];

        $this->assertEquals(5, count($checkedQuestions));

        $this->assertEquals($question2['correct'], $checkedQuestions[1]['correct_answer']);
        $this->assertEquals($question2['correct'], $checkedQuestions[1]['user_answer']);
        $this->assertEquals(true, $checkedQuestions[1]['is_correct']);

        $this->assertEquals($question4['correct'], $checkedQuestions[3]['correct_answer']);
        $this->assertEquals($question4['correct'], $checkedQuestions[3]['user_answer']);
        $this->assertEquals(true, $checkedQuestions[3]['is_correct']);

        $this->assertDatabaseHas('quizzes', [
            'uuid' => $quiz['uuid'],
            'user_id' => $user->id,
            'result' => 40,
        ]);

        $finishedQuiz = Quiz::where('uuid', $quiz['uuid'])->first();

        for ($i = 0; $i < 5; $i++) {
            $this->assertDatabaseHas('question_quiz', [
                'quiz_id' => $finishedQuiz->id,
                'question_id' => Question::where('uuid', $questions[$i]['uuid'])->first()->id,
                'answer' => $i % 2 == 0 ? 0 : $questions[$i]['correct'],
            ]);
        }
    }
}
