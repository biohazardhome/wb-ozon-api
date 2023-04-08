<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\WB\IncomeUpload;
use App\Jobs\WB\OrderUpload;
use App\Jobs\WB\SaleUpload;
use App\Jobs\WB\StockUpload;
use App\Jobs\WB\DetailReportUpload;
use App\Jobs\WB\PriceUpload;
use App\Jobs\OzonUpload;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new IncomeUpload)->dailyAt('00:00');
        $schedule->job(new OrderUpload)->dailyAt('00:00');
        $schedule->job(new SaleUpload)->dailyAt('00:00');
        $schedule->job(new StockUpload)->dailyAt('00:00');
        $schedule->job(new DetailReportUpload)->dailyAt('00:00');
        $schedule->job(new PriceUpload)->dailyAt('00:00');
        
        $schedule->job(new OzonUpload)->dailyAt('00:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
