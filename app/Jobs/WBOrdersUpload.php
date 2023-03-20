<?php

namespace App\Jobs;

use Dakword\WBSeller\API;
use App\Jobs\WBUpload;

class WBOrdersUpload extends WBUpload
{
    public $timeout = 10000;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(API $api): void
    {
        $stats = $api->Statistics();
        $this->uploadOrders($stats);        
    }

}
