<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'delivery_order_id',
        'customer_id',
        'courier_id',
        'vehicle_id',
        'origin_hub_id',
        'destination_hub_id',
        'current_hub_id',
        'sender_name',
        'sender_phone',
        'sender_address',
        'sender_city',
        'recipient_name',
        'recipient_phone',
        'recipient_province',
        'recipient_city',
        'recipient_district',
        'recipient_subdistrict',
        'recipient_postal_code',
        'recipient_address',
        'service_type',
        'weight_kg',
        'dimensions',
        'declared_value',
        'shipping_fee',
        'insurance_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'status',
        'pod_receiver_name',
        'pod_receiver_relation',
        'pod_photo',
        'pod_signature',
        'pod_delivered_at',
        'pod_latitude',
        'pod_longitude',
        'pod_location_name',
        'pod_notes',
    ];

    protected function casts(): array
    {
        return [
            'pod_delivered_at' => 'datetime',
        ];
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function originHub()
    {
        return $this->belongsTo(BranchHub::class, 'origin_hub_id');
    }

    public function destinationHub()
    {
        return $this->belongsTo(BranchHub::class, 'destination_hub_id');
    }

    public function currentHub()
    {
        return $this->belongsTo(BranchHub::class, 'current_hub_id');
    }

    public function trackingLogs()
    {
        return $this->hasMany(ShipmentTrackingLog::class)->orderBy('created_at', 'asc');
    }

    public function latestLog()
    {
        return $this->hasOne(ShipmentTrackingLog::class)->latestOfMany();
    }
}
