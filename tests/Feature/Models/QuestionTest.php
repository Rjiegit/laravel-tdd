<?php

namespace Tests\Feature\Models;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\QuestionWasUpdated;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_question_has_many_answers()
    {
        $question = Question::factory()->create();
        Answer::factory()->create(['question_id' => $question->id]);

        $this->assertInstanceOf(HasMany::class, $question->answers());
    }

    public function test_questions_with_published_at_date_are_published()
    {
        $publishedQuestion1 = Question::factory()->published()->create();

        $publishedQuestion2 = Question::factory()->published()->create();

        $unpublishedQuestion = Question::factory()->unpublished()->create();

        $publishedQuestions = Question::published()->get();

        $this->assertTrue($publishedQuestions->contains($publishedQuestion1));
        $this->assertTrue($publishedQuestions->contains($publishedQuestion2));

        $this->assertFalse($publishedQuestions->contains($unpublishedQuestion));
    }

    public function test_can_mark_an_answer_as_best()
    {
        $question = Question::factory(['best_answer_id' => null])->create();

        $answer = Answer::factory(['question_id' => $question->id])->create();

        $question->markAsBestAnswer($answer);

        $this->assertEquals($question->best_answer_id, $answer->id);

    }

    public function test_a_question_belongs_to_a_creator()
    {
        $question = Question::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $question->creator());
        $this->assertInstanceOf(User::class, $question->creator);
    }

    public function test_can_publish_a_question()
    {
        $question = Question::factory(['published_at' => null])->create();

        $this->assertCount(0, Question::query()->published()->get());

        $question->publish();

        $this->assertCount(1, Question::query()->published()->get());
    }

    public function test_it_can_detect_all_invited_users()
    {
        $question = Question::factory(['content' => '@Jane @Luke please help me!'])->create();

        $this->assertEquals(['Jane', 'Luke'], $question->invitedUsers());
    }

    public function test_questions_without_published_at_date_are_drafts()
    {
        $user = User::factory()->create();

        $draft1 = Question::factory([
            'user_id' => $user->id,
            'published_at' => null,
        ])->create();

        $draft2 = Question::factory([
            'user_id' => $user->id,
            'published_at' => null,
        ])->create();

        $publishedQuestion = Question::factory([
            'user_id' => $user->id,
            'published_at' => now()
        ])->create();

        $drafts = Question::drafts($user->id)->get();

        $this->assertTrue($drafts->contains($draft1));
        $this->assertTrue($drafts->contains($draft2));
        $this->assertFalse($drafts->contains($publishedQuestion));

    }

    public function test_question_has_answers_count()
    {
        $question = Question::factory()->create();

        Answer::factory([
            'question_id' => $question->id
        ])->create();

        $this->assertEquals(1, $question->refresh()->answers_count);
    }

    public function test_a_question_has_many_subscriptions()
    {
        $question = Question::factory()->create();

        Subscription::factory()
            ->count(2)
            ->create([
                'question_id' => $question->id,
            ]);

        $this->assertInstanceOf(HasMany::class, $question->subscriptions());
    }

    public function test_a_user_can_unsubscribe_from_questions()
    {
        $this->signIn();

        $question = Question::factory()->published()->create();

        $this->post('/questions/' . $question->id . '/subscriptions');
        $this->delete('/questions/' . $question->id . '/subscriptions');

        $this->assertCount(0, $question->subscriptions);
    }

    public function test_question_can_be_subscribed_to()
    {
        $user = User::factory()->create();

        $question = Question::factory([
            'user_id' => $user->id,
        ])->create();

        $question->subscribe($user->id);

        $this->assertEquals(1, $question->subscriptions()->where('user_id', $user->id)->count());
    }

    public function test_question_can_be_unsubscribed_from()
    {
        $user = User::factory()->create();

        $userId = $user->id;

        $question = Question::factory()->create([
            'user_id' => $userId,
        ]);

        $question->subscribe($userId);

        $question->unsubscribe($userId);

        $this->assertEquals(0, $question->subscriptions()->where('user_id', $userId)->count());
    }

    public function question_can_add_answer()
    {
        $question = Question::factory()->create();

        $question->addAnswer([
            'content' => Answer::factory()->create()->content,
            'user_id' => User::factory()->create()->id
        ]);

        $this->assertEquals(1, $question->refresh()->answers()->count());
    }

    public function test_notify_all_subscribers_when_an_answer_is_added()
    {
        Notification::fake();

        $user = User::factory()->create();

        $question = Question::factory()->create();

        $question
            ->subscribe($user->id)
            ->addAnswer([
                'content' => 'Foobar',
                'user_id' => 999
            ]);

        Notification::assertSentTo($user, QuestionWasUpdated::class);
    }

}
