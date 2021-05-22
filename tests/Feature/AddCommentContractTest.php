<?php

namespace Tests\Feature;

use App\Events\PostComment;
use App\Models\User;
use App\Notifications\YouWereMentionedInComment;
use Notification;

trait AddCommentContractTest
{
    public function test_a_notification_is_sent_when_a_comment_is_added()
    {
        Notification::fake();

        $john = User::factory()->create([
            'name' => 'John'
        ]);

        $model = $this->getCommentModel();

        $model->comment("@John Thank you", $john);

        Notification::assertSentTo($john, YouWereMentionedInComment::class);
    }

    public function test_an_event_is_dispatched_when_a_comment_is_added()
    {
        \Event::fake();

        $user = User::factory()->create();

        $model = $this->getCommentModel();

        $model->comment('it is a content', $user);

        \Event::assertDispatched(PostComment::class);
    }

    abstract protected function getCommentModel();
}
