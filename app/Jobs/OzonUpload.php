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
        $response = $api->getResponse();
        if ($response->successful()) {
            $items = $result['result']['items'];
            foreach($items as $item) {
                if (!ProductInfoStock::whereProductId($item['product_id'])->first()) { 
                    ProductInfoStock::create($item);
                }
            }
        } else {
            $response->throw();
        }

        

        $result = $this->postingFbsListV3();
        $response = $api->getResponse();
        $i = 1;

        while ($result['result']['has_next']) {

            $result2 = $this->postingFbsListV3($i * 1000);
            $postings = array_merge($result['result']['postings'], $result2['result']['postings']);
            $result['result']['postings'] = $postings;
            $result['result']['has_next'] = $result2['result']['has_next'];

            $i++;
        }

        foreach($result['result']['postings'] as $item) {
            if (!PostingFbsListV3::wherePostingNumber($item['posting_number'])->first()) {
                PostingFbsListV3::create($item);
            }
        }
    }

    public function postingFbsListV3(int $offset = 0) {

        $result = $this->api->postingFbsListV3([
            'since' => '2022-11-01T00:00:00.000Z',
            'status' => '',
            'to' => Carbon::now()->toRfc3339String(),
        ], $offset);

        $response = $this->api->getResponse();

        if ($response->successful()) {
            return $result;
        } else {
            $response->throw();
        }
    }
}
