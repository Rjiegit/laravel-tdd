<?php

namespace Tests\Feature\Questions;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeQuestionsTest extends TestCase
{

    use RefreshDatabase;

    public function test_quests_may_not_subscribe_to_or_unsubscribe_from_questions()
    {
        $question = Question::factory()->create();

        $this->post('/questions/' . $question->id . '/subscriptions')
            ->assertRedirect('login');

        $this->delete('/questions/' . $question->id . '/subscriptions')
            ->assertRedirect('/login');
    }

    public function test_a_user_can_subscribe_to_questions()
    {
        $this->signIn();

        $question = Question::factory()->published()->create();

        $this->post('/questions/' . $question->id . '/subscriptions');

        $this->assertCount(1, $question->subscriptions);
    }
}
