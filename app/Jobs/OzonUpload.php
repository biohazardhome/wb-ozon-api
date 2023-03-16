<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Ozon\OzonApi;
use App\Models\Ozon\ProductStock;
use App\Models\Ozon\Post;
use App\Models\Ozon\Cancellation;
use App\Models\Ozon\DeliveryMethod;
use App\Models\Ozon\Requirement;
use App\Models\Ozon\Product;

class OzonUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const DATE_SINCE = '2022-01-01T00:00:00.000Z';

    private $api = null;

    public $timeout = 10000;

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

        $result = $api->productInfoStocksV3([
            'offer_id' => [],
            'product_id' => [],
            'visibility' =>  'ALL',
        ]);
        $response = $api->getResponse();
        if ($response->successful()) {
            $items = $result['result']['items'];

            // dd($items = $result['result']);
            foreach($items as $item) {
                if (!ProductStock::whereProductId($item['product_id'])->first()) { 
                    ProductStock::create($item);
                }
            }
        } else {
            $response->throw();
        }

        $dateSince = Post::max('created_at');
        $dateSince = $dateSince ?? self::DATE_SINCE;
        $dateSince = Carbon::parse($dateSince);

        $dates = $this->dates($dateSince);
        foreach ($dates as [$dateSince, $dateTo]) {
            dump($dateSince->toRfc3339String(), $dateTo->toRfc3339String());
            $this->postDate($dateSince, $dateTo);
        }
        // dd($dates);

    }

    public function dates(Carbon $dateSince) {
        $dates = [];

        // $dateSince = Carbon::parse($dateSince);
        $dateTo = Carbon::now();

        $diff = $dateTo->floatDiffInYears($dateSince);
        if ($diff > 1) {
            for($i = 0; $i < floor($diff); $i++) {
                $dateTo = clone($dateSince);
                if ($i === 0) {
                    $dateTo->addYear(1)->subSecond(1);
                }
                $dates[] = [$dateSince, $dateTo];
                // dump($dateSince->toRfc3339String(), $dateTo->toRfc3339String());
                $dateSince = $dateTo;
            }
            $dateTo = Carbon::now();
            $dates[] = [$dateSince, $dateTo];
            // dump($dateSince->toRfc3339String(), $dateTo->toRfc3339String());
        } else {
            $dates[] = [$dateSince, $dateTo];
        }

        return $dates;
    }

    public function postDate(Carbon $dateSince, Carbon $dateTo) {
        $result = $this->post($dateSince, $dateTo);
        $response = $this->api->getResponse();
        $i = 1;

        while ($result['result']['has_next']) {
            $result2 = $this->post($dateSince, $dateTo, $i * 1000);
            $postings = array_merge($result['result']['postings'], $result2['result']['postings']);
            $result['result']['postings'] = $postings;
            $result['result']['has_next'] = $result2['result']['has_next'];

            $i++;
        }

        foreach($result['result']['postings'] as $item) {
            // dd($item);

            $cancellation = $item['cancellation'];
            // dump($cancellation);
            if ($cancellation) {
                // $cancellation['cancel_reason_id']
                $cancellationModel = Cancellation::whereCancelReasonId($cancellation['cancel_reason_id'])->first();
                if (!$cancellationModel) {
                    $cancellationModel = Cancellation::create($cancellation);
                }
            } else {
                $cancellationModel = Cancellation::whereCancelReasonId(0)->first();
            }
            unset($item['cancellation']);

            $delivery_method = $item['delivery_method'];
            // dump($delivery_method);
            if ($delivery_method) {
                $deliveryMethodModel = DeliveryMethod::find($delivery_method['id']);
                if (!$deliveryMethodModel) {
                    $deliveryMethodModel = DeliveryMethod::create($delivery_method);
                }
            }/* else {
                $deliveryMethodModel = DeliveryMethod::find();
            }*/
            unset($item['delivery_method']);

            $requirement = $item['requirements'];
            // dump($requirement);
            if ($requirement) {
                $requirementModel = Requirement::create($requirement);                
            }

            $products = $item['products'];
            // dump($products);
            if ($products) {
                $productIds = [];
                foreach($products as $product) {
                    $productModel = Product::firstOrCreate([
                            'sku' => $product['sku'],
                        ],
                        $product
                    );
                    // dump($productModel);
                    $productIds[] = $productModel->id;
                }

                // dump($productIds);
            }
            unset($item['products']);

            // if (!Post::wherePostingNumber($item['posting_number'])->first()) {
            $postModel = Post::create($item);
            // $postModel = Post::create($item);
            // $postModel = new Post;
            // $postModel->fill($item);
            // $postModel = new Post($item);
            // dump($cancellationModel);
            $postModel->cancellation()->associate($cancellationModel);
            $postModel->delivery_method()->associate($deliveryMethodModel);
            $postModel->requirement()->associate($requirementModel);
            $postModel->products()->sync($productIds);
            $postModel->save();

                // dd();
            // }
        }
    }

    public function addCancellation($cancellation) {

    }

    public function post(Carbon $dateSince, Carbon $dateTo, int $offset = 0) {
        $result = $this->api->postingFbsListV3([
            'delivery_method_id' => [],
            'provider_id' => [],
            'since' => $dateSince->toRfc3339String(),
            'status' => '',
            'to' => $dateTo->toRfc3339String(),
            'status' => '',
            'warehouse_id' => [],
        ],
        $offset,
        with: [
            'analytics_data' => true,
            'barcodes' => true,
            'financial_data' => true,
        ]);
        // dump(count($result['result']['postings']));
        $response = $this->api->getResponse();

        if ($response->successful()) {
            return $result;
        } else {
            $response->throw();
        }
    }
}
