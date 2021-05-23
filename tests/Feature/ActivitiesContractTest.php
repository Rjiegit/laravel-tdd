<?php

namespace Tests\Feature;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ActivitiesContractTest
{
    public function test_has_many_activities()
    {
        $model = $this->getActivityModel();

        Activity::factory()->create([
            'user_id' => $model->user_id,
            'subject_id' => $model->id,
            'subject_type' => $model->getMorphClass(),
            'type' => $this->getActivityType(),
        ]);

        $this->assertInstanceOf(MorphMany::class, $model->activities());

    }

    abstract protected function getActivityModel();

    abstract protected function getActivityType();

}
