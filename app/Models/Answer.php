<?php

namespace App\Models;

use App\Models\Traits\CommentTrait;
use App\Models\Traits\VoteTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    use VoteTrait;
    use CommentTrait;

    protected $guarded = ['id'];
    protected $appends = [
        'upVotesCount',
        'downVotesCount'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->question->increment('answers_count');
        });
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isBest()
    {
        return $this->id === $this->question->best_answer_id;
    }

}
