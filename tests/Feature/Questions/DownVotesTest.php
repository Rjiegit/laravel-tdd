<?php

namespace Tests\Feature\Questions;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\VoteDownContractTest;
use Tests\TestCase;

class DownVotesTest extends TestCase
{
    use RefreshDatabase;
    use VoteDownContractTest;

    protected function getVoteDownUri($question = null)
    {
        return $question ? "/questions/{$question->id}/down-votes" : '/questions/1/up-votes';
    }

    protected function downVotes($question)
    {
        return $question->refresh()->votes('vote_down')->get();
    }

    protected function getModel()
    {
        return Question::class;
    }
}
