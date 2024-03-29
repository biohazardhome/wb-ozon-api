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
use App\Notifications\Ozon;

use App\Models\Ozon\ {
    ProductStock,
    Cancellation,
    Post,
    DeliveryMethod,
    Requirement,
    Product,
    PostAnalytic,
    PostFinancial,
    PostFinancialService,
    PostFinancialProduct,
};

// use Fiber;

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
        $items = $result['result']['items'];

        foreach($items as $item) {
            ProductStock::updateOrCreatePrimary($item);
        }

        $dateSince = Post::max('created_at');
        $dateSince = $dateSince ?? self::DATE_SINCE;
        $dateSince = Carbon::parse($dateSince);

        $postings = $api->posts($dateSince);
        
        foreach($postings as $item) {

            $cancellation = $this->cancellation($item);
            $deliveryMethod = $this->deliveryMethod($item);
            $requirement = $this->requirement($item);
            $productIds = $this->products($item);
            $analytic = $this->analytic($item);
            $financial = $this->financial($item);

            unset($item['cancellation']);
            unset($item['delivery_method']);
            unset($item['requirements']);
            unset($item['products']);
            unset($item['analytics_data']);
            unset($item['financial_data']);

            $postModel = Post::updateOrCreate(
                ['posting_number' => $item['posting_number']],
                $item
            );

            // $postModel = Post::updateOrCreatePrimary($item);
            // $postModel->primaryDefault();

            $postModel->cancellation()->associate($cancellation);
            $postModel->delivery_method()->associate($deliveryMethod);
            $postModel->requirement()->associate($requirement);
            // $postModel->products()->sync($productIds);
            $postModel->analytic()->associate($analytic);
            $postModel->financial()->associate($financial);
            $postModel->save();
        }
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
                $productModel = Product::updateOrCreate(
                    ['sku' => $product['sku']],
                    $product
                );

                $productIds[] = $productModel->id;
            }
        }

        return $productIds;
    }

    public function analytic(array $item) {
        $analytic = $item['analytics_data'];
        if ($analytic) {
            $analyticModel = PostAnalytic::create($analytic);
        }

        return $analyticModel;
    }

    public function financial(array $item) {
        $financial = $item['financial_data'];
        if ($financial) {
            $financialModel = PostFinancial::create($financial);
            $products = $financial['products'];

            if ($products) {
                // $productIds = [];
                foreach($products as $product) {
                    // $productModel = $financialModel->products()->create($product);
                    // dump($product['product_id']);
                    $productModel = PostFinancialProduct::updateOrCreate(
                        ['product_id' => $product['product_id']],
                        $product
                    );

                    $financialModel->products()->attach($productModel->id);
                    if ($product['item_services']) {
                        // $productModel->service()->create($product['item_services']);
                    }
                    // $productIds[] = $productModel->id;
                }
                // dump($productIds);
                // $financialModel->products()->sync($productIds);
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
