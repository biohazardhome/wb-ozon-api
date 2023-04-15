<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WB\Price;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Income extends Model
{
    use HasFactory, HasCompositeKey;

    protected
        $table = 'wb_incomes',
        $primaryKey = ['income_id', 'barcode'],
        $fillable = [
            'income_id',
            'number',
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'quantity',
            'total_price',
            'date_close',
            'warehouse_name',
            'nm_id',
            'status',
        ],
        $casts = [
            'income_id' => 'integer',
            'number' => 'string',
            'date' => 'date',
            'last_change_date' => 'datetime:Y-m-d H:i:s.v',
            'supplier_article' => 'string',
            'tech_size' => 'string',
            'barcode' => 'string',
            'quantity' => 'integer',
            'total_price' => 'decimal:2',
            'date_close' => 'date',
            'warehouse_name' => 'string',
            'nm_id' => 'integer',
            'status' => 'string',
        ],
        $dates = [
            'last_change_date',
        ];

    public function price() {
        return $this->hasOne(Price::class, 'nm_id', 'nm_id');
    }

    
}
