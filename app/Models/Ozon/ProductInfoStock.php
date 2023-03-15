<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInfoStock extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'product_id',
            'offer_id',
            'stocks',
        ],
        $casts = [
            'product_id' => 'integer',
            'offer_id' => 'string',
            'stocks' => 'json',
        ];
}
