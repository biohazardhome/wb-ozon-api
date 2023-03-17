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

            foreach($items as $item) {
                ProductStock::updateOrCreate(
                    ['product_id' => $item['product_id']],
                    $item
                );
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
    }

    public function dates(Carbon $dateSince) {
        $dates = [];

        $dateTo = Carbon::now();

        $diff = $dateTo->floatDiffInYears($dateSince);
        if ($diff > 1) {
            for($i = 0; $i < floor($diff); $i++) {
                $dateTo = clone($dateSince);
                if ($i === 0) {
                    $dateTo->addYear(1)->subSecond(1);
                }
                $dates[] = [$dateSince, $dateTo];
                $dateSince = $dateTo;
            }
            $dateTo = Carbon::now();
            $dates[] = [$dateSince, $dateTo];
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

            $cancellation = $item['cancellation'];
            if ($cancellation) {
                $cancellationModel = Cancellation::updateOrCreate(
                    ['cancel_reason_id' => $cancellation['cancel_reason_id']],
                    $cancellation
                );
            } else {
                $cancellationModel = Cancellation::whereCancelReasonId(0)->first();
            }
            unset($item['cancellation']);

            $delivery_method = $item['delivery_method'];
            if ($delivery_method) {
                $deliveryMethodModel = DeliveryMethod::updateOrCreate(
                    ['id' => $delivery_method['id']],
                    $delivery_method
                );
            }
            unset($item['delivery_method']);

            $requirement = $item['requirements'];
            if ($requirement) {
                $requirementModel = Requirement::updateOrCreate(
                    ['products_requiring_gtd' => $requirement['products_requiring_gtd']],
                    $requirement
                );
            }

            $products = $item['products'];
            if ($products) {
                $productIds = [];
                foreach($products as $product) {
                    $productModel = Product::updateOrCreate(
                        ['sku' => $product['sku']],
                        $product
                    );
                    $productIds[] = $productModel->id;
                }
            }
            unset($item['products']);

            $postModel = Post::updateOrCreate(
                ['posting_number' => $item['posting_number']],
                $item
            );
            
            $postModel->cancellation()->associate($cancellationModel);
            $postModel->delivery_method()->associate($deliveryMethodModel);
            $postModel->requirement()->associate($requirementModel);
            $postModel->products()->sync($productIds);
            $postModel->save();
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
        $response = $this->api->getResponse();

        if ($response->successful()) {
            return $result;
        } else {
            $response->throw();
        }
    }
}
