<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
// use App\Jobs\WBUpload;
use App\Jobs\WBIncomeUpload;
use App\Jobs\WBOrdersUpload;
use App\Jobs\WBSalesUpload;
use App\Jobs\WBStocksUpload;
use App\Jobs\WBDetailReportUpload;
use App\Jobs\WBPricesUpload;
use App\Jobs\OzonUpload;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new WBIncomeUpload)->dailyAt('00:00');
        $schedule->job(new WBOrdersUpload)->dailyAt('00:00');
        $schedule->job(new WBSalesUpload)->dailyAt('00:00');
        $schedule->job(new WBStocksUpload)->dailyAt('00:00');
        $schedule->job(new WBDetailReportUpload)->dailyAt('00:00');
        $schedule->job(new WBPricesUpload)->dailyAt('00:00');
        
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
