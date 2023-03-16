<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WB\Income;
use App\Models\WB\Info;

class Sale extends Model
{
    use HasFactory;

    protected
        $fillable = [
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
            'sale_id',
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
            'total_price' => 'decimal:8,2',
            'discount_percent' => 'integer',
            'is_supply' => 'boolean',
            'is_realization' => 'boolean',
            'promo_code_discount' => 'decimal:8,2',
            'warehouse_name' => 'string',
            'country_name' => 'string',
            'oblast_okrug_name' => 'string',
            'region_name' => 'string',
            'income_id' => 'integer',
            'sale_id' => 'string',
            'odid' => 'integer',
            'spp' => 'decimal:8,2',
            'for_pay' => 'decimal:8,2',
            'finished_price' => 'decimal:8,2',
            'price_with_disc' => 'decimal:8,2',
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

    public function nm() {
        return $this->hasOne(Info::class, 'nm_id', 'nm_id');
    }
}
