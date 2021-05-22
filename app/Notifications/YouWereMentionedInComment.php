<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YouWereMentionedInComment extends Notification
{
    use Queueable;

    private $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $commentOwner = $this->comment->owner;

        return [
            'user_id' => $commentOwner->id,
            'user_name' => $commentOwner->name,
            'user_avatar' => $commentOwner->userAvatar,
            'content' => $this->comment->content,
        ];
    }
}
