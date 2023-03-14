<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    use HasFactory;

    protected
        $table = 'info',
        $fillable = [
            'nm_id',
            'price',
            'discount',
            'promoCode',
        ],
        $casts = [
            'nm_id' => 'integer',
            'price' => 'decimal:8,2',
            'discount' => 'integer',
            'promo_code' => 'decimal:8,2',
        ];
}
