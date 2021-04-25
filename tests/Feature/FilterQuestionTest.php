<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterQuestionTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_see_published_questions_without_any_filter()
    {
        $publishedQuestion = Question::factory([
            'published_at' => now()
        ])->create();

        $unpublishedQuestion = Question::factory()->create();

        $this->get('/questions')
            ->assertSee($publishedQuestion->title)
            ->assertDontSee($unpublishedQuestion->title);
    }

    public function user_can_see_published_questions_without_any_filter()
    {
        Question::factory([
            'published_at' => now()
        ])->count(10)->create();

        $unpublishedQuestion = Question::factory()->create();

        Question::factory([
            'published_at' => now()
        ])->count(30)->create();

        $publishedQuestion = Question::find(1);
        $response = $this->get('/questions');

        $response->assertSee($publishedQuestion->title);
        $response->assertDontSee($unpublishedQuestion->title);

        $result = $response->viewData('questions');
        $this->assertEquals(40, $result['total']);
        $this->assertCount(20, $result['data']);
    }

    public function test_user_can_filter_questions_by_category()
    {
        $category = Category::factory()->create();

        $questionInCategory = $this->publishQuestion(['category_id' => $category->id]);
        $questionNotInCategory = $this->publishQuestion();

        $this->get('/questions/' . $category->slug)
            ->assertSee($questionInCategory->title)
            ->assertDontSee($questionNotInCategory->title);
    }

    private function publishQuestion(array $overrides = [])
    {
        return Question::factory($overrides)->published()->create();
    }

    public function test_user_can_filter_questions_by_username()
    {
        $this->signIn($john = User::factory(['name' => 'john'])->create());

        $questionByJohn = $this->publishQuestion(['user_id' => $john->id]);
        $questionNotByJohn = $this->publishQuestion();

        $this->get('questions?by=john')
            ->assertSee($questionByJohn->title)
            ->assertDontSee($questionNotByJohn->title);
    }

    public function test_user_can_filter_questions_by_popularity()
    {
        $this->publishQuestion();

        $questionOfTwoAnswers = $this->publishQuestion();

        Answer::factory([
            'question_id' => $questionOfTwoAnswers->id
        ])->count(2)->create();


        $questionOfThreeAnswers = $this->publishQuestion();

        Answer::factory([
            'question_id' => $questionOfThreeAnswers->id
        ])->count(3)->create();

        $response = $this->get('questions?popularity=1');

        $questions = $response->viewData('questions')->items();

        $this->assertEquals(
            [3, 2, 0],
            array_column($questions, 'answers_count')
        );
    }

    public function test_a_user_can_filter_unanswered_questions()
    {
        $this->publishQuestion();
        $questionOfTwoAnswers = $this->publishQuestion();

        Answer::factory([
            'question_id' => $questionOfTwoAnswers->id
        ])->count(2)->create();

        $response = $this->get('questions?unanswered=1');

        $result = $response->viewData('questions')->toArray();

        $this->assertEquals(1, $result['total']);
    }
}
