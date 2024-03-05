<?php

namespace App\Models\Ozon;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use App\Models\Model;
use App\Models\Ozon\Model;

class Product extends Model
{
    // use HasFactory;

    protected
        $fillable = [
            'price',
            'offer_id',
            'name',
            'sku',
            'quantity',
            'mandatory_mark',
            'currency_code',
        ],
        $casts = [
            'price' => 'decimal:2',
            'offer_id' => 'string',
            'name' => 'string',
            'sku' => 'integer',
            'quantity' => 'integer',
            'mandatory_mark' => 'json',
            'currency_code' => 'string',
        ];
}
