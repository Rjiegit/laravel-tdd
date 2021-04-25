<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DownVotesTest extends TestCase
{

    use RefreshDatabase;
    use VoteDownContractTest;

    protected function getVoteDownUri($answer = null)
    {
        return $answer ? "/answers/{$answer->id}/down-votes" : '/answers/1/down-votes';
    }

    protected function getModel()
    {
        return Answer::class;
    }

    protected function downVotes($answer)
    {
        return $answer->refresh()->votes('vote_down')->get();
    }

}
