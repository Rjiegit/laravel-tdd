<?php

namespace App\Providers;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use App\Observers\AnswerObserver;
use App\Observers\QuestionObserver;
use App\Translator\CustomSlugTranslator;
use App\Translator\Translator;
use Illuminate\Support\ServiceProvider;
use View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Translator::class, CustomSlugTranslator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Question::observe(QuestionObserver::class);
        Answer::observe(AnswerObserver::class);

        View::composer('*', function ($view) {
            $view->with('categories', Category::all());
        });

        if (config('app.debug')) {
            $this->app->register('VIACreative\SudoSu\ServiceProvider');
        }
    }
}
