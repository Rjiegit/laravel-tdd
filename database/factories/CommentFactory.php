<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

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
            'content' => $this->faker->text,
            'commented_id' => $question->id,
            'commented_type' => get_class($question)
        ];
    }
}
