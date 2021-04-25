<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateQuestionsTest extends TestCase
{

    use RefreshDatabase;

    public function test_quests_may_not_create_questions()
    {
        $response = $this->post('questions')
            ->assertRedirect('login');

    }

    public function test_an_authenticated_user_can_create_new_questions()
    {
        $this->signIn();

        $question = Question::factory()->make();

        $this->assertCount(0, Question::all());
        $response = $this->post('questions', $question->toArray());

        $this->assertCount(1, Question::all());
    }

    public function test_authenticated_users_must_confirm_email_address_before_creating_questions()
    {
        $this->signIn(User::factory(['email_verified_at' => null])->create());

        $question = Question::factory()->make();

        $this->post('questions', $question->toArray())->assertRedirect(route('verification.notice'));
    }
}
