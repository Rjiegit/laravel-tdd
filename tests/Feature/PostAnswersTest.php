<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostAnswersTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_in_user_can_post_an_answer_to_a_published_question()
    {
        $question = Question::factory()->published()->create();
        $user = User::factory()->create();

        $response = $this->signIn($user)->post("/questions/{$question->id}/answers", [
            'content' => 'this is an answer.'
        ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $answer = $question->answers()->where('user_id', $user->id)->first();
        $this->assertNotNull($answer);

        $this->assertEquals(1, $question->answers()->count());

    }

    public function test_user_cannot_post_an_answer_to_a_unpublished_question()
    {
        $question = Question::factory()->unpublished()->create();

        $response = $this->signIn()->post("/questions/{$question->id}/answers", [
            'content' => 'this is an answer.'
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertEquals(0, $question->answers()->count());
        $this->assertDatabaseMissing((new Answer())->getTable(), ['question_id' => $question->id]);

    }

    public function test_content_is_required_to_post_answers()
    {
        $question = Question::factory()->published()->create();

        $response = $this->signIn()->post("/questions/$question->id/answers", [
            'content' => null
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('content');
    }

    public function test_guest_may_not_post_an_answer()
    {

        $question = Question::factory()->published()->create();

        $response = $this->post("/questions/{$question->id}/answers", [
            'content' => 'this is answer.'
        ]);

        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect('/login');
    }


}
