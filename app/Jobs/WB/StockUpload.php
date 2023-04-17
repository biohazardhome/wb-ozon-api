<?php

namespace App\Jobs\WB;

use Dakword\WBSeller\API;
use App\Jobs\WB\Upload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class StockUpload extends Upload
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Wildberries api upload stocks data');
        Artisan::call('stock-upload -v');
        $this->showPanel();
    }

}
