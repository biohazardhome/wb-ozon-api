<?php

namespace App\Console\Command\WB;

use App\Console\Command\Upload;
use Dakword\WBSeller\API;

class DetailReportUpload extends Upload
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb-upload:detail-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries api detail-report prepare jobs and call their for upload data';

    /**
     * Execute the console command.
     */
    public function handle(API $api): void
    {
        $stats = $api->Statistics();
        $this->uploadDetailReport($stats);  
    }
}
