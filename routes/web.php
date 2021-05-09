<?php

use App\Http\Controllers\AnswerCommentController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AnswerDownVotesController;
use App\Http\Controllers\AnswerUpVoteController;
use App\Http\Controllers\BestAnswerController;
use App\Http\Controllers\CommentDownVotesController;
use App\Http\Controllers\CommentUpVotesController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\PublishedQuestionController;
use App\Http\Controllers\QuestionCommentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionDownVotesController;
use App\Http\Controllers\QuestionUpVotesController;
use App\Http\Controllers\SubscribeQuestionsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('questions/{question}/comments', [QuestionCommentController::class, 'index'])
    ->name('question-comments.index');
Route::post('questions/{question}/comments', [QuestionCommentController::class, 'store'])
    ->name('question-comments.store');
Route::get('answers/{answer}/comments', [AnswerCommentController::class, 'index'])
    ->name('answer-comments.index');
Route::post('answers/{answer}/comments', [AnswerCommentController::class, 'store'])
    ->name('answer-comments.store');

Route::get('questions/{category?}', [QuestionController::class, 'index'])->name('questions.index');
Route::get('questions/create', [QuestionController::class, 'create'])->name('questions.create');
Route::post('questions', [QuestionController::class, 'store']);
Route::get('questions/{category}/{question}/{slug?}', [QuestionController::class, 'show'])->name('questions.show');

Route::post('/questions/{question}/subscriptions', [SubscribeQuestionsController::class, 'store'])
    ->name('subscribe-questions.store');

Route::delete('/questions/{question}/subscriptions', [SubscribeQuestionsController::class, 'destroy'])
    ->name('subscribe-questions.destroy');

Route::post('questions/{question}/answers', [AnswerController::class, 'store']);
Route::post('questions/{question}/published-questions', [PublishedQuestionController::class, 'store'])
    ->name('published-question.store');

Route::post('answers/{answer}/best', [BestAnswerController::class, 'store'])->name('best-answers.store');

Route::delete('answers/{answer}', [AnswerController::class, 'destroy'])->name('answers.destroy');

Route::post('answers/{answer}/up-votes', [AnswerUpVoteController::class, 'store'])->name('answer-up-votes.store');
Route::delete('answers/{answer}/up-votes', [AnswerUpVoteController::class, 'destroy'])->name('answer-up-votes.destroy');

Route::post('questions/{question}/up-votes', [QuestionUpVotesController::class, 'store'])
    ->name('question-up-votes.store');
Route::delete('questions/{question}/up-votes', [QuestionUpVotesController::class, 'destroy'])
    ->name('question-up-votes.destroy');

Route::post('questions/{question}/down-votes', [QuestionDownVotesController::class, 'store'])
    ->name('question-down-votes.store');
Route::delete('questions/{question}/down-votes', [QuestionDownVotesController::class, 'destroy'])
    ->name('question-down-votes.destroy');

Route::post('/comments/{comment}/up-votes', [CommentUpVotesController::class, 'store'])
    ->name('comment-up-votes.store');
Route::delete('/comments/{comment}/up-votes', [CommentUpVotesController::class, 'destroy'])
    ->name('comment-up-votes.destroy');

Route::post('/comments/{comment}/down-votes', [CommentDownVotesController::class, 'store'])
    ->name('comment-down-votes.store');
Route::delete('/comments/{comment}/down-votes', [CommentDownVotesController::class, 'destroy'])
    ->name('comment-down-votes.destroy');

Route::post('answers/{answer}/down-votes',
    [AnswerDownVotesController::class, 'store'])->name('answer-down-votes.store');
Route::delete('answers/{answer}/down-votes',
    [AnswerDownVotesController::class, 'destroy'])->name('answer-down-votes.destroy');

Route::get('drafts', [DraftController::class, 'index']);
