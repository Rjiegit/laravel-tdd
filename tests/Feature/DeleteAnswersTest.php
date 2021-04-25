<?php

namespace Tests\Feature;

use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteAnswersTest extends TestCase
{

    use RefreshDatabase;

    public function test_guest_cannot_delete_answer()
    {
        $answer = Answer::factory()->create();

        $this->delete(
            route('answers.destroy', ['answer' => $answer])
        )->assertRedirect('login');
    }

    public function test_unauthorized_users_cannot_delete_answers()
    {
        $this->signIn();

        $answer = Answer::factory()->create();

        $this->delete(
            route('answers.destroy', ['answer' => $answer])
        )->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authorized_users_can_delete_answers()
    {
        $this->signIn();

        $answer = Answer::factory(['user_id' => auth()->id()])->create();

        $this->delete(
            route('answers.destroy', ['answer' => $answer])
        )->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseMissing((new Answer())->getTable(), ['id' => $answer->id]);
    }
}
