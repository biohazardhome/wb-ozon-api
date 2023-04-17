<?php

namespace App\Jobs\WB;

use Dakword\WBSeller\API;
use App\Jobs\WB\Upload;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class IncomeUpload extends Upload
{

    /**
     * Execute the job.
     */
    public function handle(/*API $api*/): void
    {
        Log::info('Wildberries api upload incomes data');
        
        Artisan::call('income-upload -v');

        $this->showPanel();

        // dump(Artisan::output());

        /*Artisan::call('income-upload', [
            '-v' => true,
        ]);*/
    }

}
