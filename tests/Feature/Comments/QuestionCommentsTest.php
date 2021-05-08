<?php

namespace Tests\Feature\Comments;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class QuestionCommentsTest extends TestCase
{

    use RefreshDatabase;

    public function test_guest_may_not_comment_a_question()
    {
        $question = Question::factory()->published()->create();

        $result = $this->post(route('question-comments.store', ['question' => $question]), [
            'content' => 'This is a comment.'
        ])->assertRedirect('login');;
    }

    public function test_can_not_comment_an_unpublished_question()
    {
        $question = Question::factory()->unpublished()->create();

        $this->signIn($user = User::factory()->create());

        $response = $this->post(
            route('question-comments.store', ['question' => $question]), [
                'content' => 'This is a comment.'
            ]
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_signed_in_user_can_comment_a_published_question()
    {
        $question = Question::factory()->published()->create();

        $this->signIn($user = User::factory()->create());

        $response = $this->post(
            route('question-comments.store', ['question' => $question]), [
                'content' => 'This is a comment.'
            ]
        );

        $response->assertStatus(Response::HTTP_FOUND);

        $comment = $question->comments()->where('user_id', '=', $user->id)->first();

        $this->assertNotNull($comment);

        $this->assertEquals(1, $question->comments()->count());
    }

    public function test_content_is_required_to_comment_a_question()
    {
        $question = Question::factory()->published()->create();

        $this->signIn();

        $response = $this->post(
            route('question-comments.store', ['question' => $question]), [
                'content' => null
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors('content');
    }
}
