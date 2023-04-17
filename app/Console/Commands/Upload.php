<?php

namespace App\Console\Command;

use Illuminate\Console\Command;
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

class Upload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries upload api data';

    protected const DATE_SINCE = '2022-01-01T00:00:00.000Z';

    private $apiMethod = '';

    /**
     * Execute the console command.
     */
    public function handle(API $api) {
        
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
                    $item = (array) $item;
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
