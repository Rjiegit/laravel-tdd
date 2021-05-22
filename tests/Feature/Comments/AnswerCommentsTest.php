<?php

namespace Tests\Feature\Comments;

use App\Models\Answer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AnswerCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_quest_may_not_comment_an_answer()
    {
        $answer = Answer::factory()->create();

        $this->post(
            route('answer-comments.store', ['answer' => $answer]), [
                'content' => 'This is a comment.'
            ]
        )->assertRedirect('/login');
    }

    public function test_signed_in_user_can_comment_an_answer()
    {
        $answer = Answer::factory()->create();

        $this->signIn($user = User::factory()->create());

        $response = $this->post(
            route('answer-comments.store', ['answer' => $answer]), [
            'content' => 'This is a comment.'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $comment = $answer->comments()->where('user_id', '=', $user->id)->first();

        $this->assertNotNull($comment);

        $this->assertEquals(1, $answer->comments()->count());
    }

    public function test_content_is_required_to_comment_an_answer()
    {
        $answer = Answer::factory()->create();

        $this->signIn();

        $response = $this->post(
            route('answer-comments.store', ['answer' => $answer]), [
                'content' => null
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors('content');
    }
}
