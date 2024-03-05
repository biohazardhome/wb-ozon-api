<?php

namespace App\Models\Ozon;

use App\Models\Ozon\Model;

class PostFinancialProductPivot extends Model
{
    
    protected
        $fillable = [
            'financial_id',
            'product_id',
        ],
        $casts = [
            'financial_id' => 'integer',
            'product_id' => 'integer',
        ];
}
