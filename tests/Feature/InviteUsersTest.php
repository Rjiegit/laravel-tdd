<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteUsersTest extends TestCase
{

    use RefreshDatabase;

    public function test_invite_users_are_notified_when_publish_a_question()
    {
        $john = User::factory(['name' => 'John'])->create();
        $jane = User::factory(['name' => 'Jane'])->create();

        $this->signIn($john);

        $question = Question::factory([
            'user_id' => $john->id,
            'content' => '@Jane please help me!',
            'published_at' => null
        ])->create();

        $this->assertCount(0, $jane->notifications);
        $this->postJson(route('published-question.store', ['question' => $question]));

        $this->assertCount(1, $jane->refresh()->notifications);
    }

    public function test_all_invited_users_are_notified()
    {
        $john = User::factory(['name' => 'John'])->create();
        $jane = User::factory(['name' => 'Jane'])->create();
        $foo = User::factory(['name' => 'Foo'])->create();

        $this->signIn($john);

        $question = Question::factory([
            'user_id' => $john->id,
            'content' => '@Jane @Foo please help me!'
        ])->create();

        $this->assertCount(0, $jane->notifications);
        $this->assertCount(0, $foo->notifications);

        $this->postJson(route('published-question.store', ['question' => $question]));

        $this->assertCount(1, $jane->refresh()->notifications);
        $this->assertCount(1, $foo->refresh()->notifications);

    }
}
