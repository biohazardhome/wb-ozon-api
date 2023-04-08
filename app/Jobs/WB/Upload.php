<?php

namespace App\Jobs\WB;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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

class Upload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const DATE_SINCE = '2022-01-01T00:00:00.000Z';

    private $apiMethod = '';

    public $timeout = 10000;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     */
    public function handle(API $api): void
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
            if ($collect) {
                foreach($collect as $item) {
                    Price::updateOrCreate(
                        ['nm_id' => $item['nm_id']],
                        $item
                    );
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
        $collect = $this->transformKeys($collect);
        $collect = $collect->chunk(10);

        if ($collect) {
            foreach($collect as $items) {
                if ($model === Income::class) {
                    /*$model::updateOrCreate(
                        [
                            'income_id' => $item['income_id'],
                            'barcode' => $item['barcode']
                        ],
                        $item
                    );*/
                    $model::upsert($items->toArray(), ['income_id', 'barcode']);
                } else if ($model === Order::class) {
                    /*$model::updateOrCreate(
                        ['odid' => $item['odid']],
                        $item
                    );*/
                    $model::upsert($items->toArray(), ['odid']);
                } else if ($model === Sale::class) {
                    /*$model::updateOrCreate(
                        ['sale_id' => $item['sale_id']],
                        $item
                    );*/
                    $model::upsert($items->toArray(), ['sale_id']);
                } else if ($model === Stock::class) {
                    $model::upsert($items->toArray(), []);
                } else if ($model === ReportDetailByPeriod::class) {
                    /*$model::updateOrCreate(
                        ['rrd_id' => $item['rrd_id']],
                        $item
                    );*/
                    $model::upsert($items->toArray(), ['rrd_id']);
                }
            }
        }
    }

    protected function transformKeys(Collection $collect) {
        return $collect->map(function($item) {
            $item = (array) $item;
            $arr = [];
            foreach($item as $property => $value) {
                $property = str_replace('ID', 'Id', $property); // чтобы небыло i_d_
                $property = str_replace('SCCode', 'sccode', $property); // чтобы небыло s_c_c_ode
                $property = Str::snake($property);

                $arr[$property] = $value;
            }

            return $arr;
        });
    }
}