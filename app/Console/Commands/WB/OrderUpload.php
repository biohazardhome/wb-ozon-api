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
    protected $signature = 'wb-upload:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries api orders prepare jobs and call their for upload data';

    /**
     * Execute the console command.
     */
    public function handle(API $api): void
    {
        $stats = $api->Statistics();
        $this->uploadOrders($stats);  
    }
}
