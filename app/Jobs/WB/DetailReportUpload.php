<?php

namespace App\Jobs\WB;

use Dakword\WBSeller\API;
use App\Jobs\WB\Upload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DetailReportUpload extends Upload
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Wildberries api upload detail-report data');
        Artisan::call('detail-report-upload -v');
        $this->showPanel();
    }

}
