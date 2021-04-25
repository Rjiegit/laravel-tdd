<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ViewQuestionsTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_questions()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/questions');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_can_view_a_published_question()
    {
        $category = Category::factory()->create();
        $question = Question::factory([
            'published_at' => now()->subWeeks(1),
            'category_id' => $category->id
        ])->create();

        $response = $this->get("/questions/$category->slug/$question->id");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSee($question->title)
            ->assertSee($question->content);
    }

    public function test_user_cannot_view_unpublished_question()
    {
        $question = Question::factory(['published_at' => null])->create();

        $this->get('/questions/' . $question->id)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_can_see_answer_when_view_a_published_question()
    {
        $this->signIn();

        $cateogry = Category::factory()->create();

        $question = Question::factory([
            'published_at' => now(),
            'category_id' => $cateogry->id,
        ])->create();

        Answer::factory(['question_id' => $question->id])->count(40)->create();

        $response = $this->get("questions/$cateogry->slug/{$question->id}");

        $result = $response->viewData('answers')->toArray();

        $this->assertCount(20, $result['data']);
        $this->assertEquals(40, $result['total']);

    }

    public function test_title_is_required()
    {
        $this->signIn();

        $question = Question::factory([
            'title' => null
        ])->make();

        $response = $this->post('questions', $question->toArray());
        $response->assertRedirect();
        $response->assertSessionHasErrors('title');
    }

    public function test_content_is_required()
    {
        $this->signIn();

        $question = Question::factory(['content' => null])->make();
        $response = $this->post('questions', $question->toArray());
        $response->assertRedirect();
        $response->assertSessionHasErrors('content');
    }

    public function test_category_id_is_required()
    {
        $this->signIn();

        $question = Question::factory(['category_id' => null])->make();
        $response = $this->post('questions', $question->toArray());
        $response->assertRedirect();
        $response->assertSessionHasErrors('category_id');
    }

    public function test_category_id_is_exited()
    {
        $this->signIn();

        Category::factory(['id' => 1])->create();
        $question = Question::factory(['category_id' => 999])->make();

        $response = $this->post('questions', $question->toArray());

        $response->assertRedirect();
        $response->assertSessionHasErrors('category_id');
    }
}
