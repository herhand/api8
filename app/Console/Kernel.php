<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // $schedule->command('inspire')->hourly();
        // $schedule->command('passport:purge')->everyMinute();
        // $schedule->command('passport:purge')->hourly();

        $schedule->call(function () {
            DB::table('api_logs')
                ->where('created_at', '<', Carbon::now()->subDays(65))
                ->delete();
        })->daily()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // $schedule->command('passport:purge')
        //     ->dailyAt('07:00')
        //     ->appendOutputTo(storage_path('logs/scheduler.log'));

        $schedule->call(function () {
            DB::table('oauth_access_tokens')
                ->where('expires_at', '<', Carbon::now()->subDays(1))
                ->delete();
        })->dailyAt('07:00')
            ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
