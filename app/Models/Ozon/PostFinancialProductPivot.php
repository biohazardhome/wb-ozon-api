<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostFinancialProductPivot extends Model
{
    use HasFactory;

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
