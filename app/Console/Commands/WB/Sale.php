<?php

namespace App\Console\Commands\WB;

// use App\Console\Commands\Upload;
use Dakword\WBSeller\API;

class Sale extends Upload
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb-upload:sale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries api sales prepare jobs and call their for upload data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->uploadSales();  
    }
}
