<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'id',
            'name',
            'warehouse_id',
            'warehouse',
            'tpl_provider_id',
            'tpl_provider',
        ],
        $casts = [
            'id' => 'integer',
            'name' => 'string',
            'warehouse_id' => 'integer',
            'warehouse' => 'string',
            'tpl_provider_id' => 'integer',
            'tpl_provider' => 'string',
        ];
}
