<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Throwable;
use App\Jobs\WB\IncomeUpload;
use App\Jobs\WB\OrderUpload;
use App\Jobs\WB\SaleUpload;
use App\Jobs\WB\StockUpload;
use App\Jobs\WB\DetailReportUpload;
use App\Jobs\WB\PriceUpload;
use App\Jobs\OzonUpload;
use App\Console\Command\WB\IncomeUpload as IncomeUploadCommand;
use App\Console\Command\WB\OrderUpload as OrderUploadCommand;
use App\Console\Command\WB\SaleUpload as SaleUploadCommand;
use App\Console\Command\WB\StockUpload as StockUploadCommand;
use App\Console\Command\WB\DetailReportUpload as DetailReportUploadCommand;
use App\Console\Command\WB\PriceUpload as PriceUploadCommand;
use Frostrain\Laravel\ConsoleDebug\ConsoleOutputDebugCommand;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        IncomeUploadCommand::class,
        OrderUploadCommand::class,
        SaleUploadCommand::class,
        StockUploadCommand::class,
        DetailReportUploadCommand::class,
        PriceUploadCommand::class,
        // ConsoleOutputDebugCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        

        $schedule->call(function () {
            $batch = Bus::batch([
                new IncomeUpload,
                new OrderUpload,
                new SaleUpload,
                new StockUpload,
                new DetailReportUpload,
                new PriceUpload,
            ])->then(function (Batch $batch) {
                
            })->catch(function (Batch $batch, Throwable $e) {
                Log::error('The job failed with code: '. $e->getCode());
            })->finally(function (Batch $batch) {
                // The batch has finished executing...
            })->dispatch();

            // dump($batch);
        })->name('WB uploads')
            // ->allowFailures(false)
            ->dailyAt('00:00')
            // ->appendOutputTo(storage_path('logs/wb-jobs.log'));
            ->sendOutputTo(storage_path('logs/wb-jobs.log'))
            ->after(function() {
                dump(Artisan::output());
            })->onSuccess(function (Stringable $output) {
                echo 'Schedule batch output success'. $output;
            });


        
        $schedule->command('queue:prune-batches')->daily();

        $schedule->job(new OzonUpload)->dailyAt('00:30')/*->runInBackground()*/;

        $schedule->command('telescope:prune --hours=48')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        // $this->load(__DIR__.'/Commands/WB/Upload.php');

        require base_path('routes/console.php');
    }
}
