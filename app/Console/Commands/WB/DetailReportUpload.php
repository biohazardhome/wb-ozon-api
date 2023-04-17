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
    protected $signature = 'detail-report-upload';

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
        $this->info('Wildberries api upload detail report data');

        $stats = $api->Statistics();
        $this->uploadDetailReport($stats);  
    }
}
