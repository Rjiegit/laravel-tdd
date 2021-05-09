<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\VoteUpContractTest;
use Tests\TestCase;

class UpVotesTest extends TestCase
{
    use RefreshDatabase;
    use VoteUpContractTest;

    protected function getModel()
    {
        return Comment::class;
    }

    protected function getVoteUpUri($model = null)
    {
        return $model ? "/comments/{$model->id}/up-votes" : '/comments/1/up-votes';
    }

    protected function upVotes($model)
    {
        return $model->refresh()->votes('vote_up')->get();
    }

}
