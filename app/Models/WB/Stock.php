<?php

namespace App\Models\WB;

use App\Models\Model;
use App\Models\WB\Price;

class Stock extends Model
{

    protected
        $table = 'wb_stocks',
        $primaryKey = 'id',
        $fillable = [
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'quantity',
            'is_supply',
            'is_realization',
            'quantity_full',
            'warehouse_name',
            'nm_id',
            'subject',
            'category',
            'days_on_site',
            'brand',
            'sccode',
            'price',
            'discount',
        ],
        $casts = [
            'last_change_date' => 'datetime:Y-m-d H:i:s.v',
            'supplier_article' => 'string',
            'tech_size' => 'string',
            'barcode' => 'string',
            'quantity' => 'integer',
            'is_supply' => 'boolean',
            'is_realization' => 'boolean',
            'quantity_full' => 'integer',
            'warehouse_name' => 'string',
            'nm_id' => 'integer',
            'subject' => 'string',
            'category' => 'string',
            'days_on_site' => 'integer',
            'brand' => 'string',
            'sccode' => 'string',
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
        ],
        $dates = [
            'last_change_date',
        ];

    public function price() {
        return $this->hasOne(Price::class, 'nm_id', 'nm_id');
    }
}
