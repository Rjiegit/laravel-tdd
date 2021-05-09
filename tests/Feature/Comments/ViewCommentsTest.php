<?php

namespace Tests\Feature\Comments;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_request_all_comments_for_a_given_question()
    {
        $question = Question::factory()->create();
        Comment::factory([
            'commented_id' => $question->id,
            'commented_type' => Question::class,
        ])
            ->count(40)
            ->create();

        $response = $this->getJson(
            route('question-comments.index', ['question' => $question])
        )->json();

        $this->assertCount(10, $response['data']);
        $this->assertEquals(40, $response['total']);
    }

    public function test_can_request_all_comments_for_a_given_answer()
    {
        $answer = Answer::factory()->create();

        Comment::factory([
            'commented_id' => $answer->id,
            'commented_type' => Answer::class,
        ])->count(40)->create();


        $response = $this->getJson(
            route('answer-comments.index', ['answer' => $answer])
        )->json();

        $this->assertCount(10, $response['data']);
        $this->assertEquals(40, $response['total']);
    }
}
