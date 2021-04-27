<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuestionWasUpdated extends Notification
{
    use Queueable;

    private $question;
    private $answer;

    /**
     * Create a new notification instance.
     *
     * @param $question
     * @param $answer
     */
    public function __construct($question, $answer)
    {
        $this->question = $question;
        $this->answer = $answer;
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
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'answer_id' => $this->answer->id,
            'answer_content' => $this->answer->content,
            'user_id' => $this->answer->owner->id,
            'user_name' => $this->answer->owner->name,
            'question_id' => $this->question->id,
            'question_title' => $this->question->title,
        ];
    }
}
