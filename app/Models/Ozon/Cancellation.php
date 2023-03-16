<?php

namespace App\Models\Ozon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    use HasFactory;

    protected
        $fillable = [
            'cancel_reason_id',
            'cancel_reason',
            'cancellation_type',
            'cancelled_after_ship',
            'affect_cancellation_rating',
            'cancellation_initiator',
        ],
        $casts = [
            'cancel_reason_id' => 'integer',
            'cancel_reason' => 'string',
            'cancellation_type' => 'string',
            'cancelled_after_ship' => 'boolean',
            'affect_cancellation_rating' => 'boolean',
            'cancellation_initiator' => 'string',
        ];
}
