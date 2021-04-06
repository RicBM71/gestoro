<?php

namespace App\Console;

use Carbon\Carbon;
use App\Jobs\CalcularExistenciaJob;
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
        // $schedule->command('inspire')
        //          ->hourly();



        // $hora = env('CRON_HOUR');
        // if ($hora == '') $hora = '00:00';

        $hora = '17:00';

        // $schedule->call(function () {
        //     $dt = Carbon::now();
        //     \Log::info('task: '.$dt);
        // })->daily();

        //\Log::info($hora);

        //$schedule->job(new CalcularExistenciaJob)->monthlyOn(1,$hora)->withoutOverlapping();
        $schedule->job(new CalcularExistenciaJob)->weeklyOn(0,'23:00')->withoutOverlapping();
        //$schedule->job(new CalcularExistenciaJob)->everyMinute()->withoutOverlapping();



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
