<?php

namespace App\Jobs\WB;

use Dakword\WBSeller\API;
use App\Jobs\WB\Upload;

class IncomeUpload extends Upload
{
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
        $this->uploadIncomes($stats);        
    }

}
