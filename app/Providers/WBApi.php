<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Dakword\WBSeller\API;


class WBApi extends ServiceProvider
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
        $this->app->singleton(API::class, function(Application $app) {
            $wbSellerAPI = new API([
                'apikey' => env('WB_APIKEY'),
                'statkey' => env('WB_STATKEY'),
                // 'advkey' => 'ZZZ',
            ]);

            return $wbSellerAPI;
        });
    }
}
