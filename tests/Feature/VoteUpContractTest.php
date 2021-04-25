<?php

namespace Tests\Feature;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

trait VoteUpContractTest
{
    public function test_guest_can_note_vote_up()
    {
        $this->post($this->getVoteUpUri())
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_vote_up()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        $this->post($this->getVoteUpUri($model))
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, $this->upVotes($model));
    }

    public function can_vote_up_only_once()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        try {
            $this->post($this->getVoteUpUri($model));
            $this->post($this->getVoteUpUri($model));
        } catch (\Exception $e) {
            $this->fail('Can not vote up to same model twice.');
        }

        $this->assertCount(1, $this->upVotes($model));
    }

    public function test_an_authenticated_user_can_cancel_vote_up()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        $this->post($this->getVoteUpUri($model));

        $this->assertCount(1, $this->upVotes($model));

        $this->delete($this->getVoteUpUri($model));

        $this->assertCount(0, $this->upVotes($model));
    }

    public function test_can_know_it_if_voted_up()
    {
        $this->signIn();

        $model = $this->getModel()::factory()->create();

        $this->post($this->getVoteUpUri($model));

        $this->assertTrue($model->refresh()->isVotedUp(auth()->user()));
    }

    public function test_can_know_votes_count()
    {
        $model = $this->getModel()::factory()->create();

        $this->signIn();
        $this->post($this->getVoteUpUri($model));
        $this->assertEquals(1, $model->refresh()->upVotesCount);

        $this->signIn(User::factory()->create());

        $this->post($this->getVoteUpUri($model));

        $this->assertEquals(2, $model->refresh()->upVotesCount);
    }

    abstract protected function getVoteUpUri($model = null);

    abstract protected function upVotes($model);

    abstract protected function getModel();
}
