<?php

namespace Tests\Unit;

use App\Events\PostComment;
use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use App\Notifications\YouWereMentionedInComment;
use Event;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;
use Tests\Feature\AddCommentContractTest;
use Tests\TestCase;


class QuestionTest extends TestCase
{
    use RefreshDatabase;
    use AddCommentContractTest;

    public function test_a_question_has_many_comments()
    {
        $question = Question::factory()->create();

        Comment::factory([
            'commented_id' => $question->id,
            'commented_type' => $question->getMorphClass(),
            'content' => 'it is a comment'
        ])->create();

        $this->assertInstanceOf(MorphMany::class, $question->comments());
    }

    public function test_can_comment_a_question()
    {
        $question = Question::factory()->create();

        $question->comment('it is content', User::factory()->create());

        $this->assertEquals(1, $question->refresh()->comments()->count());
    }

    public function test_can_get_comments_count_attribute()
    {
        $question = Question::factory()->create();

        $question->comment('it is content', User::factory()->create());

        $this->assertEquals(1, $question->refresh()->commentsCount);
    }

    public function test_an_event_is_dispatched_when_a_comment_is_added()
    {
        Event::fake();

        $user = User::factory()->create();

        $question = Question::factory()->create();

        $question->comment('it is a content', $user);

        Event::assertDispatched(PostComment::class);

    }

    public function test_a_notification_is_sent_when_a_comments_is_added()
    {
        Notification::fake();

        $john = User::factory()->create(['name' => 'John']);

        $question = Question::factory()->create();

        $question->comment("@John Thank you!", $john);

        Notification::assertSentTo($john, YouWereMentionedInComment::class);
    }

    public function test_can_get_comment_endpoint_attribute()
    {
        $question = Question::factory()->create();
        $question->comment('ist is content', User::factory()->create());

        $this->assertEquals("/questions/{$question->id}/comments", $question->refresh()->commentEndpoint);

    }

    protected function getCommentModel()
    {
        return Question::factory()->create();
    }
}
