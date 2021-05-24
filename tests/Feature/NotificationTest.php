<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    public function test_a_notification_is_prepared_when_a_subscribed_question_receives_a_new_answer_by_other_people()
    {
        $question = Question::factory([
            'user_id' => auth()->id(),
        ])->create();

        $question->subscribe(auth()->id());

        $this->assertCount(0, auth()->user()->notifications);

        $question->addAnswer([
            'user_id' => auth()->id(),
            'content' => 'some reply here'
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $question->addAnswer([
            'user_id' => User::factory()->create()->id,
            'content' => 'some reply here'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    public function test_a_user_can_fetch_their_unread_notifications()
    {
        $question = Question::factory()->create([
            'user_id' => auth()->id()
        ]);

        $question->subscribe(auth()->id());

        $question->addAnswer([
            'user_id' => User::factory()->create()->id,
            'content' => 'some reply here'
        ]);

        $response = $this->get(route('user-notification.index', ['user' => auth()->user()]));

        $result = $response->viewData('notifications')->toArray();

        $this->assertCount(1, $result['data']);
        $this->assertEquals(1, $result['total']);
    }

    public function test_guest_cannot_see_unread_notifications_page()
    {
        auth()->logout();

        $this->get(route('user-notification.index'))
            ->assertRedirect();
    }

    public function test_clear_unread_notifications_after_see_unread_notification_page()
    {
        $question = Question::factory()->create([
            'user_id' => auth()->id()
        ]);

        $question->subscribe(auth()->id());

        $question->addAnswer([
            'user_id' => User::factory()->create()->id,
            'content' => 'some reply here',
        ]);

        $this->assertCount(1, auth()->user()->fresh()->unreadNotifications);
        $this->get(route('user-notification.index', ['user' => auth()->user()]));

        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);
    }
}
