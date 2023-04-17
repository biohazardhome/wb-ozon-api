<?php

namespace App\Console\Command\WB;

use App\Console\Command\Upload;
use Dakword\WBSeller\API;

class OrderUpload extends Upload
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order-upload';

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
        $this->info('Wildberries api upload orders data');

        $stats = $api->Statistics();
        $this->uploadOrders($stats);  
    }
}
