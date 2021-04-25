<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PublishQuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_publish_question()
    {
        $this->signIn();

        $question = Question::factory(['user_id' => auth()->id()])->create();

        $this->assertCount(0, Question::query()->published()->get());
        $this->post(route('published-question.store', ['question' => $question]));

        $this->assertCount(1, Question::query()->published()->get());
    }

    public function test_guest_may_not_publish_questions()
    {
        $this->post(route('published-question.store', ['question' => 1]))
            ->assertRedirect('login');
    }

    public function test_only_the_question_creator_can_publish_it()
    {
        $this->signIn();

        $question = Question::factory(['user_id' => auth()->id()])->create();

        $this->signIn(User::factory()->create());

        $this->post(route('published-question.store',
            ['question' => $question]))->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertCount(0, Question::query()->published()->get());
    }
}
