<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $question = Question::factory()->create();

        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'subject_id' => $question->id,
            'subject_type' => get_class($question),
            'type' => 'published_question'
        ];
    }
}
