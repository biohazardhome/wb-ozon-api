<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ozon\Cancellation;
use App\Models\Ozon\DeliveryMethod;
use App\Models\Ozon\Requirement;
use App\Models\Ozon\Product;
use App\Models\Ozon\PostAnalytic;
use App\Models\Ozon\PostFinancial;

class Post extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'posting_number',
            'delivery_method_id',
            'cancellation_id',
            'analytic_id',
            'financial_id',
            'requirement_id',
            'order_id',
            'order_number',
            'status',
            'tracking_number',
            'tpl_integration_type',
            'in_process_at',
            'shipment_date',
            'delivering_date',
            'customer',
            'addressee',
            'barcodes',
            'is_express',
            'parent_posting_number',
            'available_actions',
            'multi_box_qty',
            'is_multibox',
            'substatus',
        ],
        $casts = [
            'posting_number' => 'string',
            'delivery_method_id' => 'integer',
            'cancellation_id' => 'integer',
            'analytic_id' => 'integer',
            'financial_id' => 'json',
            'requirement_id' => 'integer',
            'order_id' => 'integer',
            'order_number' => 'string',
            'status' => 'string',
            'tracking_number' => 'string',
            'tpl_integration_type' => 'string',
            'in_process_at' => 'datetime',
            'shipment_date' => 'datetime',
            'delivering_date' => 'datetime',
            'customer' => 'string',
            'addressee' => 'string',
            'barcodes' => 'json',
            'is_express' => 'boolean',
            'parent_posting_number' => 'string',
            'available_actions' => 'json',
            'multi_box_qty' => 'integer',
            'is_multibox' => 'boolean',
            'substatus' => 'string',
        ];

    public function cancellation() {
        return $this->belongsTo(Cancellation::class, 'cancellation_id', 'id')/*->withDefault([
            'id' => 0,
            'cancel_reason_id' => 0,
            'cancel_reason' => '',
            'cancellation_type' => '',
            'cancelled_after_ship' => false,
            'affect_cancellation_rating' => false,
            'cancellation_initiator' => '',
        ])*/;
    }

    public function delivery_method() {
        return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id', 'id');
    }

    public function requirement() {
        return $this->belongsTo(Requirement::class, 'requirement_id', 'id');
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'post_products', 'post_id', 'product_id');
    }

    public function analytic() {
        return $this->belongsTo(PostAnalytic::class, 'analytic_id', 'id');
    }

    public function financial() {
        return $this->belongsTo(PostFinancial::class, 'financial_id', 'id');
    }


}
