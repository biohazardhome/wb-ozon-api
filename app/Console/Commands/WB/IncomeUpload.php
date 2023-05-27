<?php

namespace App\Console\Command\WB;

use App\Console\Command\Upload;
use Dakword\WBSeller\API;

class IncomeUpload extends Upload
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb-upload:income';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries api incomes prepare jobs and call their for upload data';
    protected $name = 'Wildberries api upload';

    // protected $verbosity = 3;

    /**
     * Execute the console command.
     */
    public function handle(API $api): void
    {
        $stats = $api->Statistics();
        $this->uploadIncomes($stats);  
    }
}
