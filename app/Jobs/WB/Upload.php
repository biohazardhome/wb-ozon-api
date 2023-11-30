<?php

namespace App\Jobs\WB;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Bus\Batchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Console\OutputStyle;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class Upload implements ShouldQueue
{
    use /*Batchable,
        */Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        IsMonitored;

    private
        $items = null,
        $model = null;

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

        foreach($items as $item) {
            // dump($item);
            $model::upsertPrimary($item);
        }
        
        // $model::upsertPrimary($items);
    }

}
