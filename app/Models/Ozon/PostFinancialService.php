<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostFinancialService extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'marketplace_service_item_fulfillment',
            'marketplace_service_item_pickup',
            'marketplace_service_item_dropoff_pvz',
            'marketplace_service_item_dropoff_sc',
            'marketplace_service_item_dropoff_ff',
            'marketplace_service_item_direct_flow_trans',
            'marketplace_service_item_return_flow_trans',
            'marketplace_service_item_deliv_to_customer',
            'marketplace_service_item_return_not_deliv_to_customer',
            'marketplace_service_item_return_part_goods_customer',
            'marketplace_service_item_return_after_deliv_to_customer',
        ],
        $casts = [
            'marketplace_service_item_fulfillment' => 'integer',
            'marketplace_service_item_pickup' => 'integer',
            'marketplace_service_item_dropoff_pvz' => 'integer',
            'marketplace_service_item_dropoff_sc' => 'integer',
            'marketplace_service_item_dropoff_ff' => 'integer',
            'marketplace_service_item_direct_flow_trans' => 'integer',
            'marketplace_service_item_return_flow_trans' => 'integer',
            'marketplace_service_item_deliv_to_customer' => 'integer',
            'marketplace_service_item_return_not_deliv_to_customer' => 'integer',
            'marketplace_service_item_return_part_goods_customer' => 'integer',
            'marketplace_service_item_return_after_deliv_to_customer' => 'integer',
        ];
}
