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
    protected $signature = 'income-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $name = 'WB upload';

    protected $verbosity = 3;

    /**
     * Execute the console command.
     */
    public function handle(API $api): void
    {

        $this->info('Wildberries api upload incomes data');

        $stats = $api->Statistics();
        /*var_dump(count($stats->stocks(
            new \DateTime(self::DATE_SINCE)
        )));*/
        $this->uploadIncomes($stats);  
    }
}
