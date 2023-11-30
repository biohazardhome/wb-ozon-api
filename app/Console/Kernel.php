<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\OzonUpload;
use App\Console\Commands\WB\Income;
use App\Console\Commands\WB\Order;
use App\Console\Commands\WB\Sale;
use App\Console\Commands\WB\Stock;
use App\Console\Commands\WB\DetailReport;
use App\Console\Commands\WB\Price;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Stringable;
use Biohazard\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        Income::class,
        Order::class,
        Sale::class,
        Stock::class,
        DetailReport::class,
        Price::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->commands(
            'wb-upload:income',
            'wb-upload:order',
            'wb-upload:price',
            'wb-upload:sale',
            'wb-upload:stock',
            'wb-upload:detail-report',
        )->name('WB uploads')
            // ->allowFailures(false)
            // ->appendOutputTo(storage_path('logs/wb-schedule.log'))
            // ->sendOutputTo(storage_path('logs/wb-schedule.log'))
            ->storeOutput()
            ->after(function() {
                // dump(Artisan::output());
            })->onSuccess(function (Stringable $output) {
                echo 'Schedule task output success '. $output;
            })->onFailure(function (Stringable $output) {
                echo 'Schedule task output failured '. $output;
            })
            // ->everyMinute()
            ->dailyAt('21:09')
            ->run();

        $schedule->job(new OzonUpload)->dailyAt('00:30')/*->runInBackground()*/;

        $schedule->command('telescope:prune --hours=48')->daily();
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
