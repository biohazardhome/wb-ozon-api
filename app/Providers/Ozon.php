<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use App\Http\OzonApi;

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
                'Client-Id' => env('OZON_CLIENT_ID'),
                'Api-Key' => env('OZON_API_KEY'),
            ])->baseUrl('https://api-seller.ozon.ru/');
        });

        $this->app->singleton(OzonApi::class, function ($app) {
            return new OzonApi([]);
        });

        // $api->post('/v3/product/info/stocks', ['filter' => ['visibility' =>  'ALL'], 'last_id' =>  '', 'limit' =>  100])
        // $api->productInfoStocksV3(['visibility' =>  'ALL'])
    }
}
