<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ozon\PostFinancialProductService;

class PostFinancialProduct extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'commission_amount',
            'commission_percent',
            'payout',
            'product_id',
            'old_price',
            'price',
            'total_discount_value',
            'total_discount_percent',
            'actions',
            'picking',
            'quantity',
            'client_price',
            'currency_code',
        ],
        $casts = [
            'commission_amount' => 'integer',
            'commission_percent' => 'integer',
            'payout' => 'integer',
            'product_id' => 'integer',
            'old_price' => 'decimal:2',
            'price' => 'decimal:2',
            'total_discount_value' => 'decimal:2',
            'total_discount_percent' => 'decimal:2',
            'actions' => 'json',
            'picking' => 'string',
            'quantity' => 'integer',
            'client_price' => 'string',
            'currency_code' => 'string',
        ];

    public function service() {
        return $this->belongsTo(PostFinancialProductService::class, 'service_id', 'id');
    }
}
