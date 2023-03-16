<?php

namespace App\Jobs;

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
use App\Models\WB\Info;
use App\Models\WB\Order;
use App\Models\WB\Sale;
use App\Models\WB\Stock;
use App\Models\WB\ReportDetailByPeriod;

class WBUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $stats = $api->Statistics();
        $prices = $api->Prices();

        $this->upload($stats, 'incomes', Income::class);
        $this->uploadPricesInfo($prices);
        $this->upload($stats, 'ordersFromDate', Order::class);
        $this->upload($stats, 'salesFromDate', Sale::class);
        $this->upload($stats, 'stocks', Stock::class);
        $this->upload($stats, 'detailReport', ReportDetailByPeriod::class);
    }

    private function uploadPricesInfo($api) {
        $prices = $api->getPrices();
        if ($prices) {
            $collect = collect($prices);
            $collect = $this->transformKeys($collect);
            if ($collect) {
                // dump($collect);
                foreach($collect as $item) {
                    if (!Info::whereNmId($item['nm_id'])->first()) {
                        Info::create($item);
                    }
                }
            }
        }
    }

    private function upload($api, string $apiMethod, string $model) {
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

    private function getDateTime(string $model) {
        $column = $this->apiMethod === 'detailReport' ? 'rr_dt' : 'last_change_date';
        $date = new Carbon('2022-01-01T00:00:00');
        $date = $model::max($column) ? (new Carbon($model::max($column))) : $date;
        return $date;
    }

    private function creates(array $items, string $model, $date) {
        $collect = collect($items);

        $collect = $collect->filter(function($item) use ($date) {
            if ($this->apiMethod === 'detailReport') {
                $rr_dt = new Carbon($item->rr_dt);
                return $rr_dt->gt($date);
            } else {
                $lastChangeDate = new Carbon($item->lastChangeDate);
                return $lastChangeDate->gt($date);
            }
        });

        $collect = $this->transformKeys($collect);
        if ($collect) {
            foreach($collect as $item) {
                $model::create($item);
            }
        }
    }

    private function transformKeys(Collection $collect) {
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
