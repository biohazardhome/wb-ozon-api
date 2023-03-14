<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostingFbsListV3 extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'posting_number',
            'order_id',
            'order_number',
            'status',
            'delivery_method',
            'tracking_number',
            'tpl_integration_type',
            'in_process_at',
            'shipment_date',
            'delivering_date',
            'cancellation',
            'customer',
            'products',
            'addressee',
            'barcodes',
            'analytics_data',
            'financial_data',
            'is_express',
            'requirements',
            'parent_posting_number',
            'available_actions',
            'multi_box_qty',
            'is_multibox',
            'substatus',
        ],
        $casts = [
            'posting_number' => 'string',
            'order_id' => 'integer',
            'order_number' => 'string',
            'status' => 'string',
            'delivery_method' => 'json',
            'tracking_number' => 'string',
            'tpl_integration_type' => 'string',
            'in_process_at' => 'datetime',
            'shipment_date' => 'datetime',
            'delivering_date' => 'datetime',
            'cancellation' => 'json',
            'customer' => 'string',
            'products' => 'json',
            'addressee' => 'string',
            'barcodes' => 'string',
            'analytics_data' => 'datetime',
            'financial_data' => 'datetime',
            'is_express' => 'boolean',
            'requirements' => 'json',
            'parent_posting_number' => 'string',
            'available_actions' => 'json',
            'multi_box_qty' => 'integer',
            'is_multibox' => 'boolean',
            'substatus' => 'string',
        ];
}
