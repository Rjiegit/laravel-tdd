<?php

namespace Tests\Feature\Models;

use App\Models\Activity;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_activity_when_a_question_is_published()
    {
        $user = User::factory()->create();

        $question = Question::factory()->create([
            'user_id' => $user->id,
        ]);

        $question->publish();

        $this->assertDatabaseHas((new Activity())->getTable(), [
            'type' => 'published_question',
            'user_id' => $user->id,
            'subject_id' => $question->id,
            'subject_type' => Question::class,
        ]);

        $this->assertEquals(1, Activity::query()->count());
    }

    public function test_not_record_activity_when_a_question_mark_an_answer_as_best()
    {
        $user = User::factory()->create();

        /** @var Question $question */
        $question = Question::factory()->create([
            'published_at' => now()->subWeeks(1),
            'user_id' => $user->id,
        ]);

        $answer = Answer::factory()->create([
            'question_id' => $question->id
        ]);

        // record created_answer
        $this->assertEquals(1, Activity::query()->count());

        $question->markAsBestAnswer($answer);

        $this->assertDatabaseMissing((new Activity())->getTable(), [
            'type' => 'published_question',
            'user_id' => $user->id,
            'subject_id' => $question->id,
            'subject_type' => Question::class,
        ]);

        // not record published_question
        $this->assertEquals(1, Activity::query()->count());
    }

    public function test_an_activity_belongs_to_a_subject()
    {
        $activity = Activity::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $activity->subject());
    }

    public function test_it_record_activity_when_an_answer_is_created()
    {
        $user = User::factory()->create();
        $answer = Answer::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas((new Activity())->getTable(), [
            'type' => 'created_answer',
            'user_id' => $user->id,
            'subject_id' => $answer->id,
            'subject_type' => Answer::class,
        ]);

        $activity = Activity::query()->first();

        $this->assertEquals($activity->subject->id, $answer->id);
    }

}
