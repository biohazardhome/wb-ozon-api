<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Http\OzonApi;
use App\Models\Ozon\ProductStock;
use App\Models\Ozon\Post;
use App\Models\Ozon\Cancellation;
use App\Models\Ozon\DeliveryMethod;
use App\Models\Ozon\Requirement;
use App\Models\Ozon\Product;
use App\Models\Ozon\PostAnalytic;
use App\Models\Ozon\PostFinancial;
use App\Models\Ozon\PostFinancialService;
use App\Models\Ozon\PostFinancialProduct;

use Fiber;

class OzonUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const DATE_SINCE = '2024-02-01T00:00:00.000Z';

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

        // dump($dateSince);
        /*$postings = cache()->remember('ozon-api-posts', 500, function () use($api, $dateSince) {
            return $api->posts($dateSince);
        });*/
        $postings = $api->posts($dateSince);

        // dd($postings);
        $fiber = new Fiber(function() use($postings) {

        // dump($this);
        foreach($postings as $item) {

            // $input = file_get_contents('php://input');
            // stream_set_blocking(STDIN, false);
            // fopen('php://stdin', 'r');
            // $input = trim(fgets(STDIN));
            // $input = trim(fread(fopen('php://stdin','rb'), 80));

            // dump($input);

            // if ($input === 'p') {
                // Fiber::suspend('pause');
            // }

            /*if ($input === 's') {
                $fiber = Fiber::getCurrent();
                $fiber->resume('start');
            }

            if ($input === 'e') {
                
            }*/

            $cancellation = $this->cancellation($item);
            $deliveryMethod = $this->deliveryMethod($item);
            $requirement = $this->requirement($item);
            $productIds = $this->products($item);
            $analytic = $this->analytic($item);
            $financial = $this->financial($item);


            // dump($item['products']);
            unset($item['cancellation']);
            unset($item['delivery_method']);
            unset($item['requirements']);
            unset($item['products']);
            unset($item['analytics_data']);
            unset($item['financial_data']);
            // dump($item);

            // dump($productIds);

            $postModel = Post::updateOrCreate(
                ['posting_number' => $item['posting_number']],
                $item
            );

            // Post::upsert($item, 'id');
            // dump($productIds, $postModel);
            // $postModel = Post::upsertPrimary($item);
            
            $postModel->cancellation()->associate($cancellation);
            $postModel->delivery_method()->associate($deliveryMethod);
            $postModel->requirement()->associate($requirement);
            $postModel->products()->sync($productIds);
            $postModel->analytic()->associate($analytic);
            $postModel->financial()->associate($financial);
            $postModel->save();
        }

        });

        $fiber->start();
    }

    public function cancellation(array $item) {
        $cancellation = $item['cancellation'];
        if ($cancellation) {
            $cancellationModel = Cancellation::updateOrCreate(
                ['cancel_reason_id' => $cancellation['cancel_reason_id']],
                $cancellation
            );
        } else {
            $cancellationModel = Cancellation::whereCancelReasonId(0)->first();
        }

        return $cancellationModel;
    }

    public function deliveryMethod(array $item) {
        $delivery_method = $item['delivery_method'];
        if ($delivery_method) {
            $deliveryMethodModel = DeliveryMethod::updateOrCreate(
                ['id' => $delivery_method['id']],
                $delivery_method
            );
        }

        return $deliveryMethodModel;
    }

    public function requirement(array $item) {
        $requirement = $item['requirements'];
        if ($requirement) {
            $requirementModel = Requirement::updateOrCreate(
                ['products_requiring_gtd' => $requirement['products_requiring_gtd']],
                $requirement
            );
        }

        return $requirementModel;
    }

    public function products(array $item) {
        $products = $item['products'];
        if ($products) {
            $productIds = [];
            foreach($products as $product) {
                // dump($product);
                $productModel = Product::updateOrCreate(
                    ['sku' => $product['sku']],
                    $product
                );

                $productIds[] = $productModel->id;
                // $product['mandatory_mark'] = '[]';
                // unset($product['mandatory_mark']);
                // $product['mandatory_mark'] = NULL;
                // Product::upsert($product, ['sku']);
                // Product::upsert($product, ['id']);
                // $productIds[] = Product::lastInsertId();
                // dump($productIds);
            }
        }

        return $productIds;
    }

    public function analytic(array $item) {
        $analytic = $item['analytics_data'];
        // dump($analytic);
        if ($analytic) {
            $analyticModel = PostAnalytic::create($analytic);
        }

        return $analyticModel;
    }

    public function financial(array $item) {
        $financial = $item['financial_data'];
        // dump($financial);
        if ($financial) {
            $financialModel = PostFinancial::create($financial);
            $products = $financial['products'];

            if ($products) {
                $productIds = [];
                foreach($products as $product) {
                    // $productModel = $financialModel->products()->create($product);
                    $productModel = PostFinancialProduct::updateOrCreate(
                        ['product_id' => $product['product_id']],
                        $product
                    );
                    if ($product['item_services']) {
                        // $productModel->service()->create($product['item_services']);
                    }
                    $productIds[] = $productModel->id;
                }
                $financialModel->products()->sync($productIds);
            }

            if ($financial['posting_services']) {
                
                $postFinancialServiceModel = PostFinancialService::create($financial['posting_services']);
                // dd($postFinancialServiceModel);
                $financialModel->service()->associate($postFinancialServiceModel);
                // $financialModel->service()->create($postFinancialServiceModel);
            }
            $financialModel->save();
        }

        return $financialModel;
    }

    
}
