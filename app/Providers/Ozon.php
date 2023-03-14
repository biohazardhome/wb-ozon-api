<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use App\Ozon\OzonApi;

class Ozon extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        

        Http::macro('ozonApi', function () {
            return Http::withHeaders([
                'Content-Type' => 'application/json',
                'Client-Id' => '548079',
                'Api-Key' => '34ea43a2-95df-47c3-a4b8-602e98680936',
            ])->baseUrl('https://api-seller.ozon.ru/');
        });

        $this->app->singleton(OzonApi::class, function ($app) {
            return new OzonApi([]);
        });

        // $api->post('/v3/product/info/stocks', ['filter' => ['visibility' =>  'ALL'], 'last_id' =>  '', 'limit' =>  100])
        // $api->productInfoStocksV3(['visibility' =>  'ALL'])
    }
}
