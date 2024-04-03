<?php

namespace App\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Carbon\Carbon;

class OzonApi
{

    protected $response = null;
    protected $responseFormat = 'array';

    public function __construct()
    {

    }

    public function request(string $type, string $url, array $query = []): mixed {
        $response = Http::connect()->{$type}($url, $query);
        $this->response = $response;
        
        if ($response->successful()) {
            if ($this->responseFormat === 'array') {
                return $response->json();
            } else if ($this->responseFormat === 'object') {
                return $response->object();
            }
        } else {
            $response->throw();
        }
    }

    public function get(string $url, array $query = []): mixed
    {
        return $this->request('get', $url, $query);
    }

    public function post(string $url, array $query = []): mixed
    {
        return $this->request('post', $url, $query);;
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

    public function posts(Carbon $dateSince) { 
        $dates = $this->dates($dateSince);
        $postings = [];
        foreach ($dates as [$dateSince, $dateTo]) {
            dump($dateSince->toRfc3339String(), $dateTo->toRfc3339String());
            $postings = array_merge($postings, $this->postsDate($dateSince, $dateTo));
        }

        return $postings;
    }

    public function postsAsync(Carbon $dateSince) { 
        $dates = $this->dates($dateSince);
        $postings = [];

        $futures = [];
        foreach ($dates as [$dateSince, $dateTo]) {
            $future = \Amp\async(function () use($dateSince, $dateTo) {
                dump($dateSince->toRfc3339String(), $dateTo->toRfc3339String());
                return $this->postsDate($dateSince, $dateTo);
            });

            $futures[] = $future;
        }

        $responses = \Amp\Future\await($futures);

        return $responses[0];
    }

    public function dates(Carbon $dateSince) {
        $dates = [];

        $dateTo = Carbon::now();

        $diff = $dateTo->floatDiffInYears($dateSince);
        if ($diff > 1) {
            for($i = 0; $i < floor($diff); $i++) {
                $dateTo = clone($dateSince);
                $dateTo->addYear(1)->subSecond(1);
                $dates[] = [$dateSince, $dateTo];
                $dateSince = $dateTo;
            }
            $dateTo = Carbon::now();
        }
        
        $dates[] = [$dateSince, $dateTo];

        return $dates;
    }

    public function postsDate(Carbon $dateSince, Carbon $dateTo) {
        $result = $this->postsRequest($dateSince, $dateTo);
        $i = 1;

        while ($result['has_next']) {
            $result2 = $this->postsRequest($dateSince, $dateTo, $i * 1000);
            $postings = array_merge($result['postings'], $result2['postings']);
            $result['postings'] = $postings;
            $result['has_next'] = $result2['has_next'];

            $i++;
        }
        
        return $result['postings'];
    }

    public function postsRequest(Carbon $dateSince, Carbon $dateTo, int $offset = 0) {
        $result = $this->postingFbsListV3([
                'delivery_method_id' => [],
                'provider_id' => [],
                'since' => $dateSince->toRfc3339String(),
                'status' => '',
                'to' => $dateTo->toRfc3339String(),
                'status' => '',
                'warehouse_id' => [],
            ],
            $offset
        );

        return $result['result'];
    }
}
