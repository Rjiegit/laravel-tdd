<?php

namespace App\Models;

use App\Models\Traits\CommentTrait;
use App\Models\Traits\InvitedUsersTrait;
use App\Models\Traits\VoteTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    use VoteTrait;
    use CommentTrait;
    use InvitedUsersTrait;

    protected $guarded = ['id'];

    protected $appends = [
        'upVotesCount',
        'downVotesCount',
        'subscriptionsCount',
        'commentsCount',
        'commentEndpoint',
    ];

    protected $with = [
        'category',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeDrafts($query, $userId)
    {
        return $query->where('user_id', '=', $userId)->whereNull('published_at');
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function getSubscriptionsCountAttribute()
    {
        return $this->subscriptions->count();
    }


    public function markAsBestAnswer($answer)
    {
        $this->update([
            'best_answer_id' => $answer->id
        ]);
    }

    public function publish()
    {
        $this->update([
            'published_at' => now()
        ]);
    }

    public function invitedUsers()
    {
        preg_match_all('/@([^\s.]+)/', $this->content, $matches);

        return $matches[1];
    }

    public function subscribe($userId)
    {
        $this->subscriptions()->create([
            'user_id' => $userId
        ]);

        return $this;
    }

    public function unsubscribe($userId)
    {
        $this->subscriptions()
            ->where('user_id', $userId)
            ->delete();

        return $this;
    }

    public function addAnswer($answer)
    {
        $answer = $this->answers()->create($answer);

        $this
            ->subscriptions
            ->where('user_id', '!=', $answer->user_id)
            ->each
            ->notify($answer);

        return $answer;
    }

    public function isSubscribedTo($user)
    {
        if (!$user) {
            return false;
        }

        return $this->subscriptions()->where('user_id', '=', $user->id)->exists();
    }

    public function path()
    {
        return $this->slug
            ? "/questions/{$this->category->slug}/{$this->id}/{$this->slug}"
            : "/questions/{$this->category->slug}/{$this->id}";
    }


}
