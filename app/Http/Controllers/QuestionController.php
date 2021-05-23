<?php

namespace App\Http\Controllers;

use App\Filters\QuestionFilter;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'index']);
        $this->middleware('must-verify-email')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Category $category
     * @param QuestionFilter $filters
     * @param User $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(Category $category, QuestionFilter $filters, User $user)
    {

        if ($category->exists) {
            $questions = Question::published()->where('category_id', '=', $category->id);
        } else {
            $questions = Question::published();
        }

        $questions = $questions->filter($filters)->paginate(20);

        array_map(function (&$item) {
            return $this->appendAttribute($item);
        }, $questions->items());

        $activeUsers = $user->getActiveUsers();

        return view('questions.index',
            compact(
                'questions',
                'activeUsers'
            )
        );
    }

    public function create(Question $question)
    {
        $categories = Category::all();

        return view('questions.create', compact('question', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);

        $question = Question::create([
            'user_id' => auth()->id(),
            'category_id' => $request->input('category_id'),
            'title' => $request->input('title'),
            'content' => $request->input('content')
        ]);

        return redirect('/drafts')->with('flash', '建立成功');
    }

    /**
     * Display the specified resource.
     *
     * @param $questionId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($category, $questionId)
    {
        $question = Question::query()
            ->published()
            ->findOrFail($questionId);

        $answers = $question->answers()->paginate(20);

        array_map(function (&$item) {
            return $this->appendVotedAttribute($item);
        }, $answers->items());

        return view('questions.show', compact('question', 'answers'));
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

    protected function appendAttribute($item)
    {
        $user = auth()->user();

        $item->isVotedUp = $item->isVotedUp($user);
        $item->isVotedDown = $item->isVotedDown($user);
        $item->isSubscribedTo = $item->isSubscribedTo($user);

        return $item;
    }
}
