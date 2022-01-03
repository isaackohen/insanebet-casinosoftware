<?php

namespace App\Console;

use App\Console\Commands\MindepositUpdate;
use App\Console\Commands\ProcessTRXPayments;
use App\Console\Commands\PullingWallet;
use App\Console\Commands\BonusBuyState;
use App\Console\Commands\JumpOnlineStatus;
use App\Console\Commands\Quiz;
use App\Console\Commands\AffiliateCron;
use App\Console\Commands\Rain;
use App\Console\Commands\ResetWeeklyBonus;
use App\Console\Commands\WithdrawLimit3Hourly;
use App\Console\Commands\WalletReset;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command(Quiz::class)->everyThirtyMinutes();
        //$schedule->command(ResetWeeklyBonus::class)->sundays();
        $schedule->command(WalletReset::class)->daily();
        $schedule->command(AffiliateCron::class)->hourly();
        // $schedule->command(ProcessTRXPayments::class)->everyMinute();
        //$schedule->command(PullingWallet::class)->everyTwoMinutes();
		//$schedule->command(JumpOnlineStatus::class)->everyFiveMinutes();
		$schedule->command(BonusBuyState::class)->everyThirtyMinutes();
        $schedule->command(MindepositUpdate::class)->everyThirtyMinutes();
        $schedule->command(WithdrawLimit3Hourly::class)->everyThreeHours();

        $expressionRain = Cache::get('schedule:expressions:rain');
        if (!$expressionRain) {
            $randomMinuteRain = mt_rand(0, 59);

            $hourRangeRain = range(1, 23);
            shuffle($hourRangeRain);
            $randomHoursRain = array_slice($hourRangeRain, 0, 2);

            $expressionRain = $randomMinuteRain.' '.implode(',', $randomHoursRain).' * * *';
            Cache::put('schedule:expressions:rain', $expressionRain, Carbon::now()->endOfDay());
        }
        $schedule->command(Rain::class)->cron($expressionRain);


        $expressionQuiz = Cache::get('schedule:expressions:quiz');
        //Log::critical($expressionQuiz);
        if (!$expressionQuiz) {
            $randomMinuteQuiz = mt_rand(0, 59);

            $hourRangeQuiz = range(1, 23);
            shuffle($hourRangeQuiz);
            $randomHoursQuiz = array_slice($hourRangeQuiz, 0, 20);

            $expressionQuiz = $randomMinuteQuiz.' '.implode(',', $randomHoursQuiz).' * * *';
            Cache::put('schedule:expressions:quiz', $expressionQuiz, Carbon::now()->endOfDay());
        }
        $schedule->command(Quiz::class)->cron($expressionQuiz);

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
