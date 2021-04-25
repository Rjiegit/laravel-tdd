<?php

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\MustVerifyEmail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class MustVerifyEmailTest extends TestCase
{
    public function test_unveridied_user_must_verify_email_before_do_something_not_allowed()
    {
        $this->signIn(User::factory(['email_verified_at' => null])->create());

        $middleware = new MustVerifyEmail();

        $response = $middleware->handle(new Request, function ($request) {
            $this->fail('Next middleware was called.');
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(url('email/verify'), $response->getTargetUrl());
    }

    public function test_verified_user_can_continue()
    {

        $this->be(User::factory(['email_verified_at' => now()])->create());

        $request = new Request();

        $next = new class {
            public $called = false;

            public function __invoke($request)
            {
                $this->called = true;
                return $request;
            }
        };

        $middleware = new MustVerifyEmail();

        $response = $middleware->handle($request, $next);

        $this->assertTrue($next->called);
        $this->assertSame($request, $response);

    }
}
