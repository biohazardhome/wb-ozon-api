<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostProduct extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'post_id',
            'product_id',
        ],
        $casts = [
            'post_id' => 'integer',
            'product_id' => 'integer',
        ];
}
