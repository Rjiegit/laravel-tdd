<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_is_required()
    {
        $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertSessionHasErrors('name');
    }

    public function test_name_can_not_contain_other_character()
    {
        $this->post('/register', [
            'name' => '***',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('name');
    }

    public function test_name_is_at_least_two_characters()
    {
        $this->withExceptionHandling()->post('/register', [
            'name' => 'a',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('name');
    }

    public function test_name_is_no_more_than_twenty_five_characters()
    {
        $this->withExceptionHandling()->post('/register', [
            'name' => 'abcdefghijklmnopqrstuvwxyz',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('name');
    }

    public function name_must_be_unique()
    {

        User::factory()->create([
            'name' => 'john'
        ]);

        $this->withExceptionHandling()->post('/register', [
            'name' => 'john',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('name');
    }

}
