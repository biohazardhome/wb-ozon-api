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
        $cats = [
            'product_id' => 'integer',
            'offer_id' => 'string',
            'stocks' => 'json',
        ];

    public function getStocksAttribute($value) {
        return $this->fromJson($value, true);
    }

    public function setStocksAttribute(string|array|object $value) {
        if (is_string($value)) {
            $this->attributes['stocks'] = $value;
        } else if (is_array($value) || is_object($value)) {
            $this->setJsonCastable('stocks', $value);
        }
    }
}
