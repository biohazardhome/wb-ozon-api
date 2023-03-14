<?php

namespace App\Ozon;

class OzonApi
{
    protected array $config;

    private Api $app;

    public function __construct(array $config)
    {
        $this->api = new Api();
    }

    public function __call($method, $arguments)
    {
        return $this->api->{$method}(...$arguments);
    }

    public function productInfoStocksV3(array $filter, $last_id = '', int $limit = 1000) {
        return $this->post('/v3/product/info/stocks', [
            'filter' => $filter,
            'last_id' => $last_id,
            'limit' => $limit,
        ]);
    }

    public function postingFboListV2(array $filter, string $dir = 'asc', int $limit = 1000, int $offset = 0, bool $translit = false, array $with = []) {
        return $this->post('/v2/posting/fbo/list', [
            'dir' => $dir,
            'filter' => $filter,
            'limit' => $limit,
            'offset' => $offset,
            'translit' => $translit,
            // 'with' => $with,
        ]);
    }

    public function postingFbsListV3(array $filter, int $offset = 0, string $dir = 'asc', int $limit = 1000, array $with = []) {
        return $this->post('v3/posting/fbs/list', [
            'dir' => $dir,
            'filter' => $filter,
            'limit' => $limit,
            'offset' => $offset,
            // 'with' => $with,
        ]);
    }
}
