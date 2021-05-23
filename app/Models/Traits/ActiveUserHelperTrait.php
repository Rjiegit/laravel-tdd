<?php

namespace App\Models\Traits;

use App\Models\Answer;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait ActiveUserHelperTrait
{
    protected $users = [];

    protected $question_weight = 4;
    protected $answer_weight = 1;
    protected $pass_days = 7;
    protected $user_number = 6;

    protected $cache_key = 'active_users';
    protected $cache_expire_in_seconds = 60 * 60;

    public function getActiveUsers()
    {
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function () {
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        $activeUsers = $this->calculateActiveUsers();

        $this->cacheActiveUsers($activeUsers);
    }

    private function calculateActiveUsers()
    {
        $this->calculateQuestionScore();
        $this->calculateAnswerScore();

        $users = Arr::sort($this->users, function ($user) {
            return $user['score'];
        });

        $users = array_reverse($users, true);


        $users = array_slice($users, 0, $this->user_number, true);


        $activeUsers = collect();

        foreach ($users as $userId => $user) {
            $user = $this->find($userId);

            if ($user) {

                $activeUsers->push($user);
            }
        }

        return $activeUsers;
    }

    private function calculateQuestionScore()
    {
        $questionUsers = Question::query()->select(DB::raw('user_id, count(*) as question_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();
        foreach ($questionUsers as $value) {
            if ($value->question_count > 0) {
                $this->users[$value->user_id]['score'] = $value->question_count * $this->question_weight;
            }
        }
    }

    private function calculateAnswerScore()
    {
        $answerUsers = Answer::query()->select(DB::raw('user_id, count(*) as answer_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        foreach ($answerUsers as $value) {
            if ($value->answer_count > 0) {
                $answer_score = $value->answer_count * $this->answer_weight;
                if (isset($this->users[$value->user_id])) {
                    $this->users[$value->user_id]['score'] += $answer_score;
                } else {
                    $this->users[$value->user_id]['score'] = $answer_score;
                }
            }
        }
    }

    private function cacheActiveUsers($activeUsers)
    {
        Cache::put($this->cache_key, $activeUsers, $this->cache_expire_in_seconds);
    }
}
