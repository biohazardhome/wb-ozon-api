<?php

namespace App\Models\WB;

use App\Models\Model;
use App\Models\WB\Income;
use App\Models\WB\Price;

class Sale extends Model
{

    protected
        $table = 'wb_sales',
        $primaryKey = 'sale_id',
        $fillable = [
            'sale_id',
            'g_number',
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'total_price',
            'discount_percent',
            'is_supply',
            'is_realization',
            'promo_code_discount',
            'warehouse_name',
            'country_name',
            'oblast_okrug_name',
            'region_name',
            'income_id',
            'odid',
            'spp',
            'for_pay',
            'finished_price',
            'price_with_disc',
            'nm_id',
            'subject',
            'category',
            'brand',
            'is_storno',
            'sticker',
            'srid',
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
            'is_supply' => 'boolean',
            'is_realization' => 'boolean',
            'promo_code_discount' => 'decimal:2',
            'warehouse_name' => 'string',
            'country_name' => 'string',
            'oblast_okrug_name' => 'string',
            'region_name' => 'string',
            'income_id' => 'integer',
            'sale_id' => 'string',
            'odid' => 'string',
            'spp' => 'decimal:2',
            'for_pay' => 'decimal:2',
            'finished_price' => 'decimal:2',
            'price_with_disc' => 'decimal:2',
            'nm_id' => 'integer',
            'subject' => 'string',
            'category' => 'string',
            'brand' => 'string',
            'is_storno' => 'integer',
            'sticker' => 'string',
            'srid' => 'string',
        ],
        $dates = [
            'date',
            'last_change_date',
        ];

    public function income() {
        return $this->hasOne(Income::class, 'income_id', 'income_id');
    }

    public function price() {
        return $this->hasOne(Price::class, 'nm_id', 'nm_id');
    }
}
