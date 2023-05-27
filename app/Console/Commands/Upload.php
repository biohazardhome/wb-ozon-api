<?php

namespace App\Console\Command;

use Illuminate\Console\Command;
use Dakword\WBSeller\API;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\WB\Income;
use App\Models\WB\Order;
use App\Models\WB\Sale;
use App\Models\WB\Stock;
use App\Models\WB\Price;
use App\Models\WB\ReportDetailByPeriod;
use App\Jobs\WB\Upload as JobUpload;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Throwable;
use Illuminate\Support\Facades\Log;
use Dakword\WBSeller\Exception\ApiClientException;

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
    protected $description = 'Wildberries api prepare jobs and call their for upload data';

    protected const DATE_SINCE = '2022-01-01T00:00:00.000Z';

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
        $this->upload($prices, 'getPrices', Price::class);
    }

    protected function upload($api, string $apiMethod, string $model) {
        if ($apiMethod === 'detailReport') {
            $date = $this->getDateTime($model, $apiMethod);
            $dateTo = Carbon::now();

            $rrd_id = ReportDetailByPeriod::max('rrd_id');
            dump($rrd_id);

            $items = $api->{$apiMethod}($date, $dateTo, 1_000, $rrd_id);
            $i = 0;
            while (count($items) > 0) {
                $last = $items[count($items) - 1];
                // dump($last);
                sleep(2);

                try {
                    $items = $api->{$apiMethod}($date, $dateTo, 1_000, $last->rrd_id);
                    dump($i, count($items), $last->rrd_id);
                    $this->creates($items, $model, $date);
                } catch(ApiClientException $e) {
                    $this->info('sleep 5min '. $e->getMessage());
                    sleep(300);
                }
                // dump($items);
                // break;
                $i++;
            }

            return;
            // exit();
            /*if ($items) {
                $this->creates($items, $model, $date);
                return ;
            }*/
        } else if ($apiMethod === 'getPrices') {
            $items = $api->{$apiMethod}();
            // dd($items);
        } else {
            $date = $this->getDateTime($model, $apiMethod);
            $items = $api->{$apiMethod}($date);
            // dd($items);
        }

        // $items = unserialize(file_get_contents('items.txt'));

        if ($items) {
            $this->creates($items, $model);
        }
    }

    protected function getDateTime(string $model) {
        $column = $model === ReportDetailByPeriod::class ? 'rr_dt' : 'last_change_date';
        $date = $model::max($column);
        $date = $date ? $date : self::DATE_SINCE;
        $date = new Carbon($date);
        return $date;
    }

    protected function creates(array $items, string $model) {
        $collect = collect($items);
        $collect = $this->transformKeys($collect);
        $collect = $collect->chunk(10);

        if ($collect->count()) {
            // $bar = $this->output->createProgressBar($collect->count());
            // $bar->start();
            // dump('$collect->count()', $collect->count());

            $jobs = [];
            foreach($collect as $items) {

                // $items = $items->map(function (/*object */$item) {
                //     return/* (array)*/ $item;
                // });

                // $jobs[] = $this->create($model, $items);
                $job = $this->create($model, $items);
                dispatch($job);

                // $bar->advance();
            }
            // $bar->finish();

            // $batch = Bus::batch($jobs)
            //     ->then(function (Batch $batch) {
            //         // Log::info('batch success');
            //     })->catch(function (Batch $batch, Throwable $e) {
            //         Log::info('The job failed with code: '. $e->getCode());
            //     })->finally(function (Batch $batch) {
            //         // The batch has finished executing...
            //     })->dispatch();

        }
    }

    protected function create(string $model, Collection $items) {
        if ($model === ReportDetailByPeriod::class) {
            $items = $items->map(function (array $item) {
                $item['date_to'] = Carbon::parse($item['date_to'])->format(DateTime::RFC3339);
                $item['date_from'] = Carbon::parse($item['date_from'])->format(DateTime::RFC3339);
                $item['create_dt'] = Carbon::parse($item['create_dt'])->format(DateTime::RFC3339);
                $item['order_dt'] = Carbon::parse($item['order_dt'])->format(DateTime::RFC3339);
                $item['sale_dt'] = Carbon::parse($item['sale_dt'])->format(DateTime::RFC3339);
                $item['rr_dt'] = Carbon::parse($item['rr_dt'])->format(DateTime::RFC3339);

                if (empty($item['bonus_type_name'])) {
                    $item['bonus_type_name'] = null;
                }

                if (empty($item['ppvz_office_name'])) {
                    $item['ppvz_office_name'] = null;
                }

                return /*(array)*/ $item;
            });
        }

        $job = new JobUpload($items->toArray(), $model);

        return $job;
    }

    protected function transformKeys(Collection $collect) {
        return $collect->map(function($item) {
            // $itemObj = new \stdClass;
            // dd($item);
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

            return /*(object)*/ $arr;
            // dump($itemObj);
            // return $itemObj;
        });
    }
}
