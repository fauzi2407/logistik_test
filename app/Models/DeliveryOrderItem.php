<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_order_id',
        'recipient_name',
        'recipient_phone',
        'recipient_province',
        'recipient_city',
        'recipient_district',
        'recipient_subdistrict',
        'recipient_postal_code',
        'recipient_address',
        'account_ref',
        'tracking_number',
        'item_name',
        'qty',
        'unit',
        'weight_kg',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'tracking_number', 'tracking_number');
    }
}
