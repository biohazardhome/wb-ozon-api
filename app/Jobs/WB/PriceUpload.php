<?php

namespace App\Jobs\WB;

use Dakword\WBSeller\API;
use App\Jobs\WB\Upload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class PriceUpload extends Upload
{
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Wildberries api upload prices data');
        Artisan::call('wb-upload:price -v');
        $this->showPanel();
    }

}
