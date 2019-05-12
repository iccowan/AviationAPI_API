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
        '\App\Console\Commands\UpdateRoutes',
        '\App\Console\Commands\UpdateAirportData',
        '\App\Console\Commands\UpdateFAACharts',
        '\App\Console\Commands\PurgeChartDatabase',
        '\App\Console\Commands\UpdateAFD',
        '\App\Console\Commands\UpdateAirportCoords',
        '\App\Console\Commands\GetVatConnections',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('Update:VatConnections')->cron('* * * * *')->withoutOverlapping();
        $schedule->command('Routes:Update')->dailyAt('00:01')->timezone('America/New_York');
        $schedule->command('AirportData:Update')->dailyAt('00:10')->timezone('America/New_York');
        $schedule->command('Update:FAACharts')->dailyAt('01:00')->timezone('America/New_York');
        $schedule->command('Update:AFD')->dailyAt('00:30')->timezone('America/New_York');
        $schedule->command('Update:PurgeChartDatabase')->dailyAt('09:00')->timezone('America/New_York');
        $schedule->command('Update:AirportCoords')->yearly();
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
