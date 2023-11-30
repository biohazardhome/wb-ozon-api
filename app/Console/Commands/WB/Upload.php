<?php

namespace App\Console\Commands\WB;

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
use App\Models\WB\DetailReport;
use App\Jobs\WB\Upload as JobUpload;
use Dakword\WBSeller\Exception\ApiClientException;

use Illuminate\Support\Facades\Artisan;

class Upload extends Command
{
    
    protected const
        DATE_SINCE = '2022-01-01T10:00:00.000Z',
        LIMIT_ITEMS = 10_000;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected
        $options = '{--date-since='. self::DATE_SINCE .'}
                    {--limit-items='. self::LIMIT_ITEMS .' : Max limit items 100_000}
                    {--chunk=1000 : Queries per chunk}',
        
        $signature = 'wb-upload';

    private
        $api = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wildberries api prepare jobs and call their for upload data';

    public function __construct(API $api) {
        $this->signature .= ' '. $this->options;
        $this->api = $api;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        Artisan::call('wb-upload:income');
        Artisan::call('wb-upload:order');
        Artisan::call('wb-upload:price');
        Artisan::call('wb-upload:sale');
        Artisan::call('wb-upload:stock');
        Artisan::call('wb-upload:detail-report');
    }

    protected function uploadIncomes() {
        $this->api = $this->api->Statistics();
        $this->upload('incomes', Income::class);
    }

    protected function uploadOrders() {
        $this->api = $this->api->Statistics();
        $this->upload('ordersFromDate', Order::class);
    }

    protected function uploadSales() {
        $this->api = $this->api->Statistics();
        $this->upload('salesFromDate', Sale::class);
    }

    protected function uploadStocks() {
        $this->api = $this->api->Statistics();
        $this->upload('stocks', Stock::class);
    }

    protected function uploadDetailReport() {
        $this->api = $this->api->Statistics();
        $this->upload('detailReport', DetailReport::class);
    }

    protected function uploadPrices() {
        $this->api = $this->api->Prices();
        $this->upload('getPrices', Price::class);
    }

    protected function uploadDetailReportLimit($date, $dateTo, $limitItems, $rrd_id) {
        $items = retry(3, function() use($date, $dateTo, $limitItems, $rrd_id) {
            $items = $this->api->detailReport($date, $dateTo, $limitItems, $rrd_id);
            $this->creates($items, DetailReport::class, $date);

            return $items;
        }, 3000000, function(\Exception $e) {
            $this->info('sleep 5min '. $e->getMessage());
            return $e instanceof ApiClientException;
        });

        return $items;
    }

    protected function upload(string $apiMethod, string $model) {
        if ($model === DetailReport::class) {
            $date = $this->getDateTime($model);
            $dateTo = now();

            $rrd_id = DetailReport::max('rrd_id') ?? 0;
            dump($rrd_id);

            $limitItems = $this->option('limit-items');
            
            $items = $this->uploadDetailReportLimit($date, $dateTo, $limitItems, $rrd_id);

            // $i = 0;
            while (count($items) > 0) {
                $last = last($items);
                $items = $this->uploadDetailReportLimit($date, $dateTo, $limitItems, $last->rrd_id);
            
                // $i++;
            }

            return;
            
        } else if ($model === Price::class) {
            $items = $this->api->{$apiMethod}();
        } else {
            $date = $this->getDateTime($model);
            $items = $this->api->{$apiMethod}($date);
        }

        if ($items) {
            $this->creates($items, $model);
        }
    }

    protected function getDateTime(string $model) {
        $column = $model === DetailReport::class ? 'rr_dt' : 'last_change_date';
        $date = $model::max($column);
        // $date = $date ? $date : self::DATE_SINCE;
        $date = $date ? $date : $this->option('date-since');
        $date = new Carbon($date);
        return $date;
    }

    protected function creates(array $items, string $model) {
        $chunk = $this->option('chunk');
        collect($items)
            ->keysToSnake([
                'ID' => 'Id',
                'SCCode' => 'sccode',
            ])
            ->chunk($chunk)
            ->each(function($items) use($model) {
                $job = $this->create($model, $items);
                dispatch($job);
            });
    }

    protected function create(string $model, Collection $items) {
        if ($model === DetailReport::class) {
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

                return $item;
            });
        }

        $job = new JobUpload($items->toArray(), $model);

        return $job;
    }

}


