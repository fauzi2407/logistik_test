<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'do_number',
        'customer_id',
        'origin_hub_id',
        'order_date',
        'delivery_date',
        'sender_name',
        'sender_phone',
        'sender_address',
        'sender_city',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'recipient_city',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'delivery_date' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function originHub()
    {
        return $this->belongsTo(BranchHub::class, 'origin_hub_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getOriginHubAttribute()
    {
        if (!empty($this->attributes['origin_hub_id'])) {
            $hub = BranchHub::find($this->attributes['origin_hub_id']);
            if ($hub) return $hub;
        }

        if ($this->relationLoaded('shipment') && $this->shipment && $this->shipment->originHub) {
            return $this->shipment->originHub;
        }

        if ($this->relationLoaded('shipments') && $this->shipments->first() && $this->shipments->first()->originHub) {
            return $this->shipments->first()->originHub;
        }

        $s = $this->shipments()->with('originHub')->first();
        if ($s && $s->originHub) {
            return $s->originHub;
        }

        if ($this->sender_city) {
            $hub = BranchHub::where('city', 'like', "%{$this->sender_city}%")->first();
            if ($hub) return $hub;
        }

        return BranchHub::first();
    }

    public function isPickedUp(): bool
    {
        if (in_array($this->status, ['shipped', 'delivered'])) {
            return true;
        }

        return $this->items()->whereHas('shipment', function ($q) {
            $q->whereIn('status', ['picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery', 'delivered']);
        })->exists();
    }
}
