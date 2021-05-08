<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class QuestionTest extends TestCase
{
    use RefreshDatabase;

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
}
