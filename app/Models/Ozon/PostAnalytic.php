<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAnalytic extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'region',
            'city',
            'delivery_type',
            'is_premium',
            'payment_type_group_name',
            'warehouse_id',
            'warehouse',
            'tpl_provider_id',
            'tpl_provider',
            'delivery_date_begin',
            'delivery_date_end',
            'is_legal',
        ],
        $casts = [
            'region' => 'string',
            'city' => 'string',
            'delivery_type' => 'string',
            'is_premium' => 'boolean',
            'payment_type_group_name' => 'string',
            'warehouse_id' => 'integer',
            'warehouse' => 'string',
            'tpl_provider_id' => 'integer',
            'tpl_provider' => 'string',
            'delivery_date_begin' => 'date',
            'delivery_date_end' => 'date',
            'is_legal' => 'boolean',
        ];
}
