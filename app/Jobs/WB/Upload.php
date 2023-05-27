<?php

namespace App\Jobs\WB;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Console\OutputStyle;
use App\Models\WB\Income;
use App\Models\WB\Order;
use App\Models\WB\Sale;
use App\Models\WB\Stock;
use App\Models\WB\Price;
use App\Models\WB\ReportDetailByPeriod;

class Upload implements ShouldQueue
{
    use Batchable,
        Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels/*,
        IsMonitored*/;

    private
        $items = null,
        $model;

    public
        $timeout = 10000,
        $tries = 3;
        
    /**
     * Create a new job instance.
     */
    public function __construct(array $items, string $model)
    {
        $this->items = $items;
        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {   
        $items = $this->items;
        $model = $this->model;
        // dd($items, $model);

        if ($model === Income::class) {
            $model::upsert($items, ['income_id', 'barcode']);
        } else if ($model === Order::class) {
            $model::upsert($items, ['odid']);
        } else if ($model === Sale::class) {
            $model::upsert($items, ['sale_id']);
        } else if ($model === Price::class) {
            $model::upsert($this->items, ['nm_id']);
        } else if ($model === Stock::class) {
            $model::upsert($items, []);
        } else if ($model === ReportDetailByPeriod::class) {
            $model::upsert($items, ['rrd_id']);                    
        }
    }

    protected function showPanel(): void {
        if (config('app.debug')) { $this->showDebugPanelConsole(); }
    }

    protected function showDebugPanelConsole(): void {
        $command = app()->make('console-output-debug');

        $params = [];
        $input = new ArrayInput($params);
        $output = new ConsoleOutput();

        $outputStyle = new OutputStyle($input, $output);
        $outputStyle->setVerbosity(ConsoleOutput::VERBOSITY_VERBOSE);

        $command->setInput($input);
        $command->setOutput($outputStyle);

        $command->handle();
    }
}
