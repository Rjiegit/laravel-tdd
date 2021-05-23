<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CalculateActiveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate-active-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '計算活躍的使用者';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(User $user)
    {

        $this->info('start...');

        $user->calculateAndCacheActiveUsers();

        $this->info('end...');

        return 0;
    }
}
