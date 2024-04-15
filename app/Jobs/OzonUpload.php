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

use App\Models\Ozon\ProductStock;
use App\Models\Ozon\Post;

class OzonUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const DATE_SINCE = '2024-02-01T00:00:00.000Z';
    private const JOB_THREADS = 8;

    // private $api = null;

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
        $syncedAt = Carbon::now();

        $result = $api->productInfoStocksV3([
            'offer_id' => [],
            'product_id' => [],
            'visibility' =>  'ALL',
        ]);
        $items = $result['result']['items'];

        foreach($items as $item) {
            ProductStock::updateOrCreatePrimary($item);
        }

        $dateSince = Post::max('synced_at');
        $dateSince = $dateSince ?? self::DATE_SINCE;
        $dateSince = Carbon::parse($dateSince);

        // $postings = $api->posts($dateSince);
        $postings = $api->postsAsync($dateSince);

        if ($postings) {
            $chunks = array_split($postings, self::JOB_THREADS);
            foreach($chunks as $postings) {
                if ($postings) {
                    dispatch(new OzonPostStore($postings, $syncedAt));
                }
            }
        }
    }    
}
