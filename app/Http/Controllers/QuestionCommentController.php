<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionCommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')
            ->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Question $question
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Question $question)
    {
        $comments = $question->comments()->paginate(10);

        array_map(function (&$item) {
            return $this->appendVotedAttribute($item);
        }, $comments->items());

        return $comments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store($questionId)
    {

        $this->validate(request(), [
            'content' => 'required'
        ]);

        $question = Question::query()->published()->findOrFail($questionId);

        $comment = $question->comment(request()->input('content'), auth()->user());

        return $comment->load('owner');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
