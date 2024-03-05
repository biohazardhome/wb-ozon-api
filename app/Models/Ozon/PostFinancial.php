<?php

namespace App\Models\Ozon;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use App\Models\Ozon\Model;

// use App\Models\Ozon\PostFinancialProduct;
// use App\Models\Ozon\PostFinancialProductPivot;
use App\Models\Ozon\ {
    PostFinancialService,
    PostFinancialProduct,
    PostFinancialProductPivot
};

class PostFinancial extends Model
{
    // use HasFactory;

    protected
        $fillable = [
            'service_id',
            'cluster_from',
            'cluster_to',
        ],
        $casts = [
            'service_id' => 'integer',
            'cluster_from' => 'string',
            'cluster_to' => 'string',
        ];

    public function products() {
        // return $this->belongsToMany(PostFinancialProduct::class, 'post_financial_product_pivots', 'financial_id', 'product_id');
        return $this->belongsToMany(PostFinancialProduct::class, PostFinancialProductPivot::class, 'financial_id', 'product_id');
    }

    public function service() {
        return $this->belongsTo(PostFinancialService::class, 'service_id', 'id');
    }
}
