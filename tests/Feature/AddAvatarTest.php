<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_not_add_avatars()
    {

        $this->post(route('user-avatar.store', ['user' => 1]))
            ->assertRedirect('/login');
    }

    public function test_avatar_is_required()
    {

        $this->signIn();

        $this->post(
            route('user-avatar.store', ['user' => auth()->user()]),
            ['avatar' => null]
        )
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors('avatar');
    }

    public function test_avatar_must_be_valid()
    {
        $this->signIn();

        $this->post(
            route('user-avatar.store', ['user' => auth()->user()]),
            ['avatar' => 'not-an-image']
        )
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors('avatar');
    }

    public function test_poster_image_must_be_least_200px_width()
    {
        $this->signIn();

        \Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.png', 199, 516);

        $this->post(
            route('user-avatar.store', ['user' => auth()->user()]),
            ['avatar' => $file]
        )->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors('avatar');
    }

    public function test_poster_image_must_be_least_200px_height()
    {
        $this->signIn();

        \Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.png', 516, 199);

        $this->post(
            route('user-avatar.store', ['user' => auth()->user()]),
            ['avatar' => $file]
        )->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasErrors('avatar');
    }

    public function test_user_can_add_an_avatar()
    {
        $this->signIn();

        Storage::fake('public');

        $this->post(route('user-avatar.store', ['user' => auth()->user()]), [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg', 300, 300)
        ]);

        $this->assertEquals('avatars/' . $file->hashName(), auth()->user()->avatar_path);
        Storage::disk('public')->assertExists('avatars/'. $file->hashName());
    }
}
