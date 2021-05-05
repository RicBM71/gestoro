<?php

namespace App\Console;

use Carbon\Carbon;
use App\Jobs\WooProcessingJob;
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

        if (config('cron.woo_url') != false){
            $schedule->job(new WooProcessingJob)->between('08:00', '20:30')->everyThirtyMinutes()->withoutOverlapping();
            //$schedule->job(new WooProcessingJob)->everyMinute()->withoutOverlapping();
        }

        $hora = config('cron.time');

        if ($hora == '') $hora = '00:00';

        // $schedule->call(function () {
        //     $dt = Carbon::now();
        //     \Log::info('task: '.$dt);
        // })->daily();

        //\Log::info($hora);

        $schedule->job(new CalcularExistenciaJob)->weeklyOn(0, $hora)->withoutOverlapping();

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
