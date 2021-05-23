<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_active_user_by_artisan_command()
    {
        $john = User::factory()->create([
            'name' => 'john'
        ]);
        $jane = User::factory()->create([
            'name' => 'jane'
        ]);

        // john got 4 score
        $question = Question::factory()->create([
            'user_id' => $john->id
        ]);

        // jane got 1 score
        Answer::factory()->create([
            'user_id' => $jane->id,
            'question_id' => $question->id
        ]);

        User::factory()->count(10)->create();

        $this->artisan('calculate-active-user')
            ->expectsOutput('start...')
            ->expectsOutput('end...')
            ->assertExitCode(0);

        $activeUsers = Cache::get('active_users');

        $this->assertEquals(2, $activeUsers->count());

        $this->assertTrue($john->is($activeUsers[0]));
        $this->assertTrue($jane->is($activeUsers[1]));

    }
}
