<?php

namespace App\Models\WB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Carbon\Carbon;

class Income extends Model
{
    use HasFactory;

    protected
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
            'total_price' => 'decimal:8,2',
            'date_close' => 'date',
            'warehouse_name' => 'string',
            'nm_id' => 'integer',
            'status' => 'string',
        ],
        $dates = [
            'last_change_date',
        ];

    /*public function getLastChangeDateAttribute(string $value): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i:s.v', $value);
    }

    public function setLastChangeDateAttribute(Carbon $value): void
    {
        $this->attributes['last_change_date'] = $value->format('Y-m-d H:i:s.v');
    }*/
}
