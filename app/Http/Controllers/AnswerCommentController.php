<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerCommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')
            ->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Answer $answer
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Answer $answer)
    {
        $comments = $answer->comments()->paginate(10);

        array_map(function (&$item) {
            return $this->appendVotedAttribute($item);
        }, $comments->items());

        return $comments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Answer $answer
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(Answer $answer)
    {
        $this->validate(\request(), [
            'content' => 'required'
        ]);

        $comment = $answer->comment(\request()->input('content'), auth()->user());

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
