<?php


namespace App\Models\Traits;


use App\Events\PostComment;
use App\Models\Comment;

trait CommentTrait
{
    public function comment($content, $user)
    {
        $comment = $this->comments()->create([
            'user_id' => $user->id,
            'content' => $content,
        ]);

        event(new PostComment($comment));

        return $comment;
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commented');
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments->count();
    }

    public function getCommentEndpointAttribute()
    {
        return '/' . $this->getTable() . '/' . $this->id . '/comments';
    }
}
