<?php

namespace App\Providers;

use App\Events\PostComment;
use App\Events\PublishQuestion;
use App\Listeners\NotifyInvitedUsers;
use App\Listeners\NotifyMentionedUsersInComment;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PublishQuestion::class => [
            NotifyInvitedUsers::class
        ],
        PostComment::class => [
            NotifyMentionedUsersInComment::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
