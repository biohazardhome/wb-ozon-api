<?php

namespace App\Models\WB;

use App\Models\Model;
use App\Models\WB\Income;
use App\Models\WB\Price;

class Order extends Model
{

    protected
        $table = 'wb_orders',
        $primaryKey = 'odid',
        $fillable = [
            'g_number',
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'total_price',
            'discount_percent',
            'warehouse_name',
            'oblast',
            'income_id',
            'odid',
            'nm_id',
            'subject',
            'category',
            'brand',
            'is_cancel',
            'cancel_dt',
            'sticker',
            'srid',
            'order_type',
        ],
        $casts = [
            'g_number' => 'string',
            'date' => 'datetime',
            'last_change_date' => 'datetime:Y-m-d H:i:s.v',
            'supplier_article' => 'string',
            'tech_size' => 'string',
            'barcode' => 'string',
            'total_price' => 'decimal:2',
            'discount_percent' => 'integer',
            'warehouse_name' => 'string',
            'oblast' => 'string',
            'income_id' => 'integer',
            'odid' => 'integer',
            'nm_id' => 'integer',
            'subject' => 'string',
            'category' => 'string',
            'brand' => 'string',
            'is_cancel' => 'boolean',
            'cancel_dt' => 'datetime',
            'sticker' => 'string',
            'srid' => 'string',
            'order_type' => 'string',
        ],
        $dates = [
            'date',
            'last_change_date',
            'cancel_dt',
        ];

    public function income() {
        return $this->hasOne(Income::class, 'income_id', 'income_id');
    }

    public function price() {
        return $this->hasOne(Price::class, 'nm_id', 'nm_id');
    }
}
