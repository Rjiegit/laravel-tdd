<?php

namespace Tests\Feature\Models;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    public function test_a_subscription_belongs_to_a_user()
    {

        $subscription = Subscription::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $subscription->user());
    }
}
