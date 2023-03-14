<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Ozon\OzonApi;
use App\Models\Ozon\ProductInfoStock;
use App\Models\Ozon\PostingFbsListV3;
use Carbon\Carbon;

class OzonUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $api = null;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(OzonApi $api): void
    {
        $this->api = $api;
        $result = $api->productInfoStocksV3(['visibility' =>  'ALL']);
        $items = $result['result']['items'];

        foreach($items as $item) {
            if (!ProductInfoStock::whereProductId($item['product_id'])->first()) { 
                $item['stocks'] = json_encode($item['stocks']);
                ProductInfoStock::create($item);
            }
        }

        $result = $this->postingFbsListV3();
        $items = $result['result']['postings'];
        dump(count($items));
        dump($items[0]);
    }

    public function postingFbsListV3(int $offset = 0) {
        static $inc = 0;

        $result = $this->api->postingFbsListV3([
            'since' => '2022-11-01T00:00:00.000Z',
            'status' => '',
            // 'to' => '2023-03-01T23:59:59.000Z',
            'to' => Carbon::now()->toRfc3339String(),
        ], $offset);

        $items = $result['result']['postings'];
        foreach($items as $item) {
            if (!PostingFbsListV3::wherePostingNumber($item['posting_number'])->first()) {
                PostingFbsListV3::create($item);
            }
        }

        if ($result['result']['has_next']) {
            $inc++;
            $result = $this->postingFbsListV3($inc * 1000);

            $items = $result['result']['postings'];
            foreach($items as $item) {
                if (!PostingFbsListV3::wherePostingNumber($item['posting_number'])->first()) {
                    PostingFbsListV3::create($item);
                }
            }
        }

        return $result;
    }
}
