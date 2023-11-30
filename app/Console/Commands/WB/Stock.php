<?php

namespace App\Console\Commands\WB;

// use App\Console\Commands\Upload;
use Dakword\WBSeller\API;

class Stock extends Upload
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb-upload:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries api stocks prepare jobs and call their for upload data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->uploadStocks();  
    }
}
