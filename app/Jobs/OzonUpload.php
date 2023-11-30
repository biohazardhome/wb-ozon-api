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

        // dump(Post::max('created_at'), $dateSince);

        $postings = $api->posts($dateSince);
        // dd($postings);
        foreach($postings as $item) {

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

            $analytic = $item['analytics_data'];
            // dump($analytic);
            if ($analytic) {
                $analyticModel = PostAnalytic::create($analytic);
            }
            unset($item['analytics_data']);

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
            unset($item['financial_data']);

            // dump($item);

            $postModel = Post::updateOrCreate(
                ['posting_number' => $item['posting_number']],
                $item
            );
            
            $postModel->cancellation()->associate($cancellationModel);
            $postModel->delivery_method()->associate($deliveryMethodModel);
            $postModel->requirement()->associate($requirementModel);
            $postModel->products()->sync($productIds);
            $postModel->analytic()->associate($analyticModel);
            $postModel->financial()->associate($financialModel);
            $postModel->save();
        }
    }

    public function addCancellation($cancellation) {

    }

    
}
