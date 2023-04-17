<?php

namespace App\Console\Command\WB;

use App\Console\Command\Upload;
use Dakword\WBSeller\API;

class SaleUpload extends Upload
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sale-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(API $api): void
    {
        $this->info('Wildberries api upload sales data');

        $stats = $api->Statistics();
        $this->uploadSales($stats);  
    }
}
