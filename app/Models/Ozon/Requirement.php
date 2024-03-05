<?php

namespace App\Models\Ozon;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use App\Models\Ozon\Model;

class Requirement extends Model
{
    // use HasFactory;

    protected
        $fillable = [
            'products_requiring_gtd',
            'products_requiring_country',
            'products_requiring_mandatory_mark',
            'products_requiring_rnpt',
        ],
        $casts = [
            'products_requiring_gtd' => 'json',
            'products_requiring_country' => 'json',
            'products_requiring_mandatory_mark' => 'json',
            'products_requiring_rnpt' => 'json',
        ];
}
