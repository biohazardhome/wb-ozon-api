<?php

namespace App\Ozon;

use Illuminate\Support\Facades\Http;

class Api
{
    public function __construct()
    {

    }

    public function get(string $url, array $query = []): array
    {
        return Http::ozonApi()->get($url, $query)->json();
    }

    public function post(string $url, array $query = []): array
    {
        return Http::ozonApi()->post($url, $query)->json();
    }
}
