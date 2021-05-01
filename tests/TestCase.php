<?php

namespace Tests;

use App\Models\User;
use App\Translator\FakeSlugTranslator;
use App\Translator\Translator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(Translator::class, new FakeSlugTranslator());
    }

    protected function signIn($user = null)
    {
        $user = $user ?: User::factory()->create();

        $this->actingAs($user);

        return $this;
    }
}
