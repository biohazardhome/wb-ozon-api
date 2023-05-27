<?php

namespace App\Console\Command\WB;

use App\Console\Command\Upload;
use Dakword\WBSeller\API;

class PriceUpload extends Upload
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb-upload:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries api prices prepare jobs and call their for upload data';

    /**
     * Execute the console command.
     */
    public function handle(API $api): void
    {
        $prices = $api->Prices();
        $this->uploadPrices($prices);  
    }
}
