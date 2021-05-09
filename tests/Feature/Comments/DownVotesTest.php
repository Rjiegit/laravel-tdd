<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\VoteDownContractTest;
use Tests\TestCase;

class DownVotesTest extends TestCase
{
    use RefreshDatabase;
    use VoteDownContractTest;

    protected function getModel()
    {
        return Comment::class;
    }

    protected function getVoteDownUri($model = null)
    {
        return $model ? "/comments/{$model->id}/down-votes" : '/comments/1/up-votes';
    }

    protected function downVotes($model)
    {
        return $model->refresh()->votes('vote_down')->get();
    }
}
