<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_see_his_profile_page()
    {
        $user = User::factory()->create();

        $this->signIn($user);

        $this->get(
            route('users.show', ['user' => $user])
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($user->name);
    }
}
