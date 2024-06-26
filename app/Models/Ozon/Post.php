<?php

namespace App\Models\Ozon;

use App\Models\Ozon\ {
    Model,
    Cancellation,
    DeliveryMethod,
    Requirement,
    Product,
    PostProduct,
    PostAnalytic,
    PostFinancial,
};

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class Post extends Model
{

    use Notifiable;

    protected
        // $primaryKey = 'posting_number',
        $touches = ['products'],
        $fillable = [
            'posting_number',
            'delivery_method_id',
            'cancellation_id',
            'analytic_id',
            'financial_id',
            'requirement_id',
            'order_id',
            'order_number',
            'status',
            'tracking_number',
            'tpl_integration_type',
            'in_process_at',
            'shipment_date',
            'delivering_date',
            'customer',
            'addressee',
            'barcodes',
            'is_express',
            'parent_posting_number',
            'available_actions',
            'multi_box_qty',
            'is_multibox',
            'substatus',
            'serialize',
            'synced_at',
        ],
        $casts = [
            'posting_number' => 'string',
            'delivery_method_id' => 'int',
            'cancellation_id' => 'integer',
            'analytic_id' => 'integer',
            'financial_id' => 'integer',
            'requirement_id' => 'integer',
            'order_id' => 'integer',
            'order_number' => 'string',
            'status' => 'string',
            'tracking_number' => 'string',
            'tpl_integration_type' => 'string',
            'in_process_at' => 'datetime',
            'shipment_date' => 'datetime',
            'delivering_date' => 'datetime',
            'customer' => 'string',
            'addressee' => 'string',
            'barcodes' => 'object',
            'is_express' => 'boolean',
            'parent_posting_number' => 'string',
            'available_actions' => 'json',
            'multi_box_qty' => 'integer',
            'is_multibox' => 'boolean',
            'substatus' => 'string',
            'synced_at' => 'datetime',
        ];

    public function cancellation() {
        return $this->belongsTo(Cancellation::class, 'cancellation_id', 'id')/*->withDefault([
            'id' => 0,
            'cancel_reason_id' => 0,
            'cancel_reason' => '',
            'cancellation_type' => '',
            'cancelled_after_ship' => false,
            'affect_cancellation_rating' => false,
            'cancellation_initiator' => '',
        ])*/;
    }

    public function delivery_method() {
        return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id', 'id');
    }

    public function requirement() {
        return $this->belongsTo(Requirement::class, 'requirement_id', 'id');
    }

    public function products() {
        return $this->belongsToMany(Product::class, PostProduct::class)
            ->withTimestamps();
    }

    public function analytic() {
        return $this->belongsTo(PostAnalytic::class, 'analytic_id', 'id');
    }

    public function financial() {
        return $this->belongsTo(PostFinancial::class, 'financial_id', 'id');
    }



    /*public function routeNotificationForMail(Notification $notification): array|string
    {
        // Вернуть адрес электронной почты и имя ...
        return ['stalker-nikko@yandex.ru' => 'Nikolay'];
    }*/

    /*public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null) {
        if (is_string($related)) {
                $classes = get_declared_classes();
            foreach ($classes as $class) {
                if (str_ends_with($class, $related)) {
                    $related = $class;
                }
            }

            if (!class_exists($related)) {
                throw new \Exception('Not exists class', 1);
            }

            // dump($related);
        }

        return parent::belongsToMany($related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation);
    }*/


}
