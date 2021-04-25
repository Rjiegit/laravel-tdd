<?php

namespace Tests\Feature;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

trait VoteDownContractTest
{
    public function test_guest_can_not_vote_down()
    {
        $this->post($this->getVoteDownUri())
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_vote_down()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        $this->post($this->getVoteDownUri($model))
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, $this->downVotes($model));
    }

    public function test_can_vote_down_only_once()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        try {
            $this->post($this->getVoteDownUri($model));
            $this->post($this->getVoteDownUri($model));
        } catch (\Exception $e) {
            $this->fail('Can not vote up to same model twice.');
        }

        $this->assertCount(1, $this->downVotes($model));
    }

    public function test_an_authenticated_user_can_cancel_vote_down()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        $this->post($this->getVoteDownUri($model));

        $this->assertCount(1, $this->downVotes($model));

        $this->delete($this->getVoteDownUri($model));

        $this->assertCount(0, $this->downVotes($model));
    }

    public function test_can_know_it_if_vote_down()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        $this->post($this->getVoteDownUri($model));

        $this->assertTrue($model->refresh()->isVotedDown(auth()->user()));
    }

    public function test_can_know_down_votes_count()
    {
        $model = $this->getModel()::factory()->create();

        $this->signIn();
        $this->post($this->getVoteDownUri($model));
        $this->assertEquals(1, $model->refresh()->downVotesCount);

        $this->signIn(User::factory()->create());
        $this->post($this->getVoteDownUri($model));

        $this->assertEquals(2, $model->refresh()->downVotesCount);
    }

    abstract protected function getVoteDownUri($model = null);

    abstract protected function downVotes($model);

    abstract protected function getModel();
}
