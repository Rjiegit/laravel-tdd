<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ViewDraftsTest extends TestCase
{

    public function test_guests_may_not_view_drafts()
    {
        $this->get('/drafts')->assertRedirect('/login');
    }

    public function test_user_can_view_drafts()
    {
        $this->signIn($user = User::factory()->create());

        $question = Question::factory([
            'published_at' => null,
            'user_id' => $user->id
        ])->create();

        $this->get('/drafts')
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($question->title);
    }

    public function test_only_the_creator_can_view_it()
    {
        $john = User::factory(['name' => 'john'])->create();
        $jane = User::factory(['name' => 'jane'])->create();

        $questionWithJohn = Question::factory(['user_id' => $john->id])->create();
        $questionWithJane = Question::factory(['user_id' => $jane->id])->create();

        $this->signIn($john);

        $this->get('/drafts')
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($questionWithJohn->title)
            ->assertDontSee($questionWithJane->title);
    }

    public function test_cannot_see_a_published_question_in_drafts()
    {
        $this->signIn($user = User::factory()->create());

        $question = Question::factory([
            'published_at' => now(),
            'user_id' => $user->id
        ])->create();

        $this->get('/drafts')
            ->assertStatus(Response::HTTP_OK)
            ->assertDontSee($question->title);
    }
}
