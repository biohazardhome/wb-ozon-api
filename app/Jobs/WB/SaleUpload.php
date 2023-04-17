<?php

namespace App\Jobs\WB;

use Dakword\WBSeller\API;
use App\Jobs\WB\Upload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SaleUpload extends Upload
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Wildberries api upload sales data');
        Artisan::call('sale-upload -v');
        $this->showPanel();
    }

}
