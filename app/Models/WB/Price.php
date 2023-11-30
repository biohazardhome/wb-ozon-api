<?php

namespace App\Models\WB;

use App\Models\Model;

class Price extends Model
{

    protected
        $table = 'wb_prices',
        $primaryKey = 'nm_id',
        $fillable = [
            'nm_id',
            'price',
            'discount',
            'promo_code',
        ],
        $casts = [
            'nm_id' => 'integer',
            'price' => 'decimal:2',
            'discount' => 'integer',
            'promo_code' => 'decimal:2',
        ];
}
