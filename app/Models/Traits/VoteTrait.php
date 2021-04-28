<?php


namespace App\Models\Traits;


use App\Models\User;
use App\Models\Vote;

trait VoteTrait
{

    public function getUpVotesCountAttribute()
    {
        return $this->votes('vote_up')->count();
    }

    public function getDownVotesCountAttribute()
    {
        return $this->votes('vote_down')->count();
    }

    public function voteUp(User $user)
    {
        $attributes = ['user_id' => $user->id];

        if (!$this->votes('vote_up')->where($attributes)->exists()) {
            $this->votes('vote_up')->create(['user_id' => $user->id, 'type' => 'vote_up']);
        }
    }

    public function cancelVoteUp(User $user)
    {
        $this->votes('vote_up')->where(['user_id' => $user->id, 'type' => 'vote_up'])->delete();
    }

    public function votes($type)
    {
        return $this->morphMany(Vote::class, 'voted')->whereType($type);
    }

    public function isVotedUp($user)
    {
        if (!$user) {
            return false;
        }

        return $this->votes('vote_up')->where('user_id', $user->id)->exists();
    }

    public function voteDown($user)
    {
        $isVotedDown = $this->votes('vote_down')->where(['user_id' => $user->id])->exists();
        if ($isVotedDown) {
            return;
        }
        $this->votes('vote_down')->create(['user_id' => $user->id, 'type' => 'vote_down']);
    }

    public function cancelVoteDown($user)
    {
        $this->votes('vote_down')->where(['user_id' => $user->id, 'type' => 'vote_down'])->delete();
    }

    public function isVotedDown($user)
    {
        if (!$user) {
            return false;
        }

        return $this->votes('vote_down')->where('user_id', $user->id)->exists();
    }

}
