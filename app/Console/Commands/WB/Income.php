<?php

namespace App\Console\Commands\WB;

// use App\Console\Commands\WB\Upload;
use Dakword\WBSeller\API;

class Income extends Upload
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
    public function handle(): void
    {
        $this->uploadIncomes();
    }
}
