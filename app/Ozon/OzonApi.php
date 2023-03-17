<?php

namespace App\Ozon;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class OzonApi
{

    protected $response = null;

    public function __construct()
    {

    }

    public function get(string $url, array $query = []): array
    {
        $response = Http::ozonApi()->get($url, $query);
        $this->response = $response;
        return $response->json();
    }

    public function post(string $url, array $query = []): array
    {
        $response = Http::ozonApi()->post($url, $query);
        $this->response = $response;
        return $response->json();
    }

    public function getResponse(): Response {
        return $this->response;
    }

    public function productInfoStocksV3(array $filter, $last_id = '', int $limit = 1000): array {
        return $this->post('/v3/product/info/stocks', [
            'filter' => $filter,
            'last_id' => $last_id,
            'limit' => $limit,
        ]);
    }

    public function postingFboListV2(array $filter, string $dir = 'asc', int $limit = 1000, int $offset = 0, bool $translit = false, array $with = []): array {
        return $this->post('/v2/posting/fbo/list', [
            'dir' => $dir,
            'filter' => $filter,
            'limit' => $limit,
            'offset' => $offset,
            'translit' => $translit,
            // 'with' => $with,
        ]);
    }

    public function postingFbsListV3(array $filter, int $offset = 0, string $dir = 'asc', int $limit = 1000, array $with = []): array {
        return $this->post('v3/posting/fbs/list', [
            'dir' => $dir,
            'filter' => $filter,
            'limit' => $limit,
            'offset' => $offset,
            'with' => [
                'analytics_data' => true,
                'barcodes' => true,
                'financial_data' => true,
            ],
        ]);
    }
}
