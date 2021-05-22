<?php

namespace Tests\Feature\Comments;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{

    use RefreshDatabase;

    public function test_mentioned_users_are_notified_when_comment_a_question()
    {
        $john = User::factory()->create(['name' => 'John']);
        $jane = User::factory()->create(['name' => 'Jane']);
        $foo = User::factory()->create(['name' => 'Foo']);

        $this->signIn($john);

        $question = Question::factory()->create(['published_at' => now()]);

        $this->assertCount(0, $jane->notifications);
        $this->assertCount(0, $foo->notifications);

        $this->postJson(route('question-comments.store', ['question' => $question]), [
            'content' => '@Jane @Foo please help me!'
        ]);

        $this->assertCount(1, $jane->refresh()->notifications);
        $this->assertCount(1, $foo->refresh()->notifications);

    }
}
