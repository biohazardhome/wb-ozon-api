<?php

namespace App\Jobs\WB;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Dakword\WBSeller\API;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\WB\Income;
use App\Models\WB\Price;
use App\Models\WB\Order;
use App\Models\WB\Sale;
use App\Models\WB\Stock;
use App\Models\WB\ReportDetailByPeriod;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Console\OutputStyle;

class Upload implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const DATE_SINCE = '2022-01-01T00:00:00.000Z';

    private $apiMethod = '';

    public
        $timeout = 10000,
        $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /*$stats = $api->Statistics();
        $prices = $api->Prices();

        $this->upload($stats, 'incomes', Income::class);
        $this->uploadPrices($prices);
        $this->upload($stats, 'ordersFromDate', Order::class);
        $this->upload($stats, 'salesFromDate', Sale::class);
        $this->upload($stats, 'stocks', Stock::class);
        $this->upload($stats, 'detailReport', ReportDetailByPeriod::class);*/

    }

    protected function showPanel(): void {
        if (config('app.debug')) { $this->showDebugPanelConsole(); }
    }

    protected function showDebugPanelConsole(): void {
        $command = app()->make('console-output-debug');

        $params = [];
        $input = new ArrayInput($params);
        $output = new ConsoleOutput();

        $outputStyle = new OutputStyle($input, $output);
        $outputStyle->setVerbosity(ConsoleOutput::VERBOSITY_VERBOSE);

        $command->setInput($input);
        $command->setOutput($outputStyle);

        $command->handle();
    }

    protected function uploadIncomes($stats) {
        $this->upload($stats, 'incomes', Income::class);
    }

    protected function uploadOrders($stats) {
        $this->upload($stats, 'ordersFromDate', Order::class);
    }

    protected function uploadSales($stats) {
        $this->upload($stats, 'salesFromDate', Sale::class);
    }

    protected function uploadStocks($stats) {
        $this->upload($stats, 'stocks', Stock::class);
    }

    protected function uploadDetailReport($stats) {
        $this->upload($stats, 'detailReport', ReportDetailByPeriod::class);;
    }

    protected function uploadPrices($prices) {
        $prices = $prices->getPrices();
        if ($prices) {
            $collect = collect($prices);
            $collect = $this->transformKeys($collect);
            if ($collect->count()) {
                $collect = $collect->chunk(10);
                foreach($collect as $items) {
                    Price::upsert($items->toArray(), ['nm_id']);
                }
            }
        }
    }

    protected function upload($api, string $apiMethod, string $model) {
        $this->apiMethod = $apiMethod;

        $date = $this->getDateTime($model, $apiMethod);
        
        if ($apiMethod === 'detailReport') {
            $dateTo = Carbon::now();
            $items = $api->{$apiMethod}($date, $dateTo, 100000);
        } else {
            $items = $api->{$apiMethod}($date);
        }

        if ($items) {
            $this->creates($items, $model, $date);
        }
    }

    protected function getDateTime(string $model) {
        $column = $this->apiMethod === 'detailReport' ? 'rr_dt' : 'last_change_date';
        $date = new Carbon(self::DATE_SINCE);
        $date = $model::max($column) ? (new Carbon($model::max($column))) : $date;
        return $date;
    }

    protected function creates(array $items, string $model, $date) {
        $collect = collect($items);
        if ($collect->count()) {
            $collect = $this->transformKeys($collect);
            $collect = $collect->chunk(10);

            foreach($collect as $items) {

                $items = $items->map(function (object $item/*, int $key*/) {
                    return (array) $item;
                });

                if ($model === Income::class) {
                    $model::upsert($items->toArray(), ['income_id', 'barcode']);
                } else if ($model === Order::class) {
                    $model::upsert($items->toArray(), ['odid']);
                } else if ($model === Sale::class) {
                    $model::upsert($items->toArray(), ['sale_id']);
                } else if ($model === Stock::class) {
                    $model::upsert($items->toArray(), []);
                } else if ($model === ReportDetailByPeriod::class) {

                    $items = $items->map(function (array $item) {
                        $item = (object) $item;
                        $item->date_to = Carbon::parse($item->date_to)->format(DateTime::RFC3339);
                        $item->date_from = Carbon::parse($item->date_from)->format(DateTime::RFC3339);
                        $item->create_dt = Carbon::parse($item->create_dt)->format(DateTime::RFC3339);
                        $item->order_dt = Carbon::parse($item->order_dt)->format(DateTime::RFC3339);
                        $item->sale_dt = Carbon::parse($item->sale_dt)->format(DateTime::RFC3339);
                        $item->rr_dt = Carbon::parse($item->rr_dt)->format(DateTime::RFC3339);

                        if (empty($item->bonus_type_name)) {
                            $item->bonus_type_name = null;
                        }

                        if (empty($item->ppvz_office_name)) {
                            $item->ppvz_office_name = null;
                        }

                        return (array) $item;
                    });

                    $model::upsert($items->toArray(), ['rrd_id']);
                }
            }
        }
    }

    protected function transformKeys(Collection $collect) {
        return $collect->map(function($item) {
            // $itemObj = new \stdClass;
            $item = (array) $item;
            $arr = [];
            foreach($item as $property => $value) {
                $property = str_replace('ID', 'Id', $property); // чтобы небыло i_d_
                $property = str_replace('SCCode', 'sccode', $property); // чтобы небыло s_c_c_ode
                $property = Str::snake($property);

                $arr[$property] = $value;
                // dump($property, $value);
                // $itemObj->{$property} = $value;
            }

            return (object) $arr;
            // dump($itemObj);
            // return $itemObj;
        });
    }
}
