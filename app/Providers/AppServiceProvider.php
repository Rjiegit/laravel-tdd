<?php

namespace App\Providers;

use App\Models\Question;
use App\Observers\QuestionObserver;
use App\Translator\CustomSlugTranslator;
use App\Translator\Translator;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.debug')) {
            $this->app->register('VIACreative\SudoSu\ServiceProvider');
        }

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
    }
}
