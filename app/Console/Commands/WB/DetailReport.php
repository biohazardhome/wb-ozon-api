<?php

namespace App\Console\Commands\WB;

// use App\Console\Commands\Upload;
use Dakword\WBSeller\API;

class DetailReport extends Upload
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
    public function handle(): void
    {
        $this->uploadDetailReport();  
    }
}
