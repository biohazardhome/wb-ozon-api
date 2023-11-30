<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Arr;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*Artisan::command('income-upload', function(){
    
});*/


Artisan::command('test', function(){
    try {
        // $memoryStart = memory_get_peak_usage(false);



        start_memory_measure('Some Loop');
        // $this->line('hello!');

        \App\Models\User::first();
        \App\Models\WB\Income::all();

        stop_memory_measure('Some Loop');



        /*$memoryEnd = memory_get_peak_usage(false);
        $memory = $memoryEnd - $memoryStart;
        // dump($memoryStart, $memoryEnd, $memory);
        $memory = debugbar()
            ->getCollector('memory_details')
            ->getDataFormatter()
            ->formatBytes($memory);

        dump($memory);*/
    } catch (Throwable $e) {
        Debugbar::addThrowable($e);
        echo $e->getMessage() .' line: '. $e->getLine();
    }
});