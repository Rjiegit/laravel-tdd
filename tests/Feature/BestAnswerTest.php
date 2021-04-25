<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BestAnswerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_not_mark_best_answer()
    {
        $question = Question::factory()->create();

        $answers = Answer::factory(['question_id' => $question->id])->count(2)->create();

        $this->post(
            route('best-answers.store', ['answer' => $answers[1]]),
            [$answers[1]]
        )
            ->assertRedirect('/login');

    }

    public function test_can_mark_one_answer_as_the_best()
    {
        $this->signIn();
        $question = Question::factory(['user_id' => auth()->id()])->create();

        $answers = Answer::factory(['question_id' => $question->id])->count(2)->create();

        $this->assertFalse($answers[0]->isBest());
        $this->assertFalse($answers[1]->isBest());

        $this->postJson(
            route('best-answers.store', ['answer' => $answers[1]]),
            [$answers[1]]
        );

        $this->assertFalse($answers[0]->refresh()->isBest());
        $this->assertTrue($answers[1]->refresh()->isBest());
    }

    public function test_only_the_question_creator_can_mark_a_best_answer()
    {
        $this->signIn();

        $question = Question::factory(['user_id' => auth()->id()])->create();

        $answer = Answer::factory(['question_id' => $question->id])->create();

        // another user
        $this->signIn(User::factory()->create());

        $this->postJson(
            route('best-answers.store', ['answer' => $answer]),
            [$answer]
        )->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertFalse($answer->fresh()->isBest());
    }
}
