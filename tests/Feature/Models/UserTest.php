<?php

namespace Tests\Feature\Models;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_an_avatar_path()
    {
        $user = User::factory()->create([
            'avatar_path' => 'http://example.com/avatar.png',
        ]);

        $this->assertEquals('http://example.com/avatar.png', $user->avatar_path);
    }

    public function test_user_can_determine_avatar_path()
    {
        $user = User::factory()->create();
        $this->assertEquals(url('storage/avatars/default.png'), $user->avatar());

        $user->avatar_path = 'avatars/me.png';
        $this->assertEquals(url('storage/avatars/me.png'), $user->avatar());
    }

    public function test_can_get_user_avatar_attribute()
    {
        $user = User::factory()->create([
            'avatar_path' => 'avatars/example.png'
        ]);

        $this->assertEquals(url('storage/avatars/example.png'), $user->userAvatar);
    }

    public function test_a_user_has_many_activities()
    {
        $user = User::factory()->create();

        Activity::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(HasMany::class, $user->activities());
    }
}
