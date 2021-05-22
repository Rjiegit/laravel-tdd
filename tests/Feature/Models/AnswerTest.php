<?php

namespace Tests\Feature\Models;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\AddCommentContractTest;
use Tests\TestCase;

class AnswerTest extends TestCase
{
    use RefreshDatabase;
    use AddCommentContractTest;

    public function test_it_knows_if_it_is_the_best()
    {
        $answer = Answer::factory()->create();

        $this->assertFalse($answer->isBest());

        $answer->question->update(['best_answer_id' => $answer->id]);

        $this->assertTrue($answer->isBest());
    }

    public function test_an_answer_belongs_to_a_question()
    {
        $answer = Answer::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $answer->question());
    }

    public function test_an_answer_belongs_to_an_owner()
    {
        $answer = Answer::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $answer->owner());
        $this->assertInstanceOf(User::class, $answer->owner);
    }

    public function test_can_vote_up_an_answer()
    {
        $this->signIn();

        $answer = Answer::factory()->create();

        $this->assertDatabaseMissing('votes', [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_up'
        ]);

        $answer->voteUp(auth()->user());

        $this->assertDatabaseHas('votes', [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_up'
        ]);
    }

    public function test_cancel_vote_up_an_answer()
    {
        $this->signIn();

        $answer = Answer::factory()->create();

        $answer->voteUp(auth()->user());

        $answer->cancelVoteUp(auth()->user());


        $this->assertDatabaseMissing('votes', [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer)
        ]);
    }

    public function test_can_know_it_is_voted_up()
    {
        $user = User::factory()->create();

        $answer = Answer::factory()->create();

        Vote::factory([
            'user_id' => $user->id,
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
        ])->create();

        $this->assertTrue($answer->refresh()->isVotedUp($user));
    }

    public function test_can_vote_down_an_answer()
    {
        $this->signIn();

        $answer = Answer::factory()->create();

        $this->assertDatabaseMissing((new Vote())->getTable(), [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_down',
        ]);

        $answer->voteDown(auth()->user());

        $this->assertDatabaseHas((new Vote())->getTable(), [
            'user_id' => auth()->id(),
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_down',
        ]);

    }

    public function test_can_cancel_vote_down_answer()
    {
        $this->signIn();

        $answer = Answer::factory()->create();

        $answer->voteDown(auth()->user());

        $answer->cancelVoteDown(auth()->user());

        $this->assertDatabaseMissing(
            (new Vote())->getTable(),
            [
                'user_id' => auth()->id(),
                'voted_id' => $answer->id,
                'voted_type' => get_class($answer)
            ]
        );
    }

    public function test_can_know_it_is_voted_down()
    {
        $user = User::factory()->create();

        $answer = Answer::factory()->create();

        Vote::factory([
            'user_id' => $user->id,
            'voted_id' => $answer->id,
            'voted_type' => get_class($answer),
            'type' => 'vote_down'
        ])->create();

        $this->assertTrue($answer->refresh()->isVotedDown($user));
    }

    public function test_can_comment_an_answer()
    {
        $answer = Answer::factory()->create();

        $answer->comment('content', User::factory()->create());

        $this->assertEquals(1, $answer->refresh()->comments()->count());
    }

    public function test_an_answer_has_many_comments()
    {
        $answer = Answer::factory()->create();

        Comment::factory([
            'commented_id' => $answer->id,
            'commented_type' => $answer->getMorphClass(),
            'content' => 'it is a comment',
        ])->create();

        $this->assertInstanceOf(MorphMany::class, $answer->comments());
    }

    public function test_can_get_comments_count_attribute()
    {
        $answer = Answer::factory()->create();

        $answer->comment('it is content', User::factory()->create());

        $this->assertEquals(1, $answer->refresh()->commentsCount);
    }

    public function test_can_get_comment_endpoint_attribute()
    {
        $answer = Answer::factory()->create();

        $answer->comment('it is content', User::factory()->create());

        $this->assertEquals("/answers/{$answer->id}/comments",$answer->refresh()->commentEndpoint);

    }

    protected function getCommentModel()
    {
        return Answer::factory()->create();
    }
}
