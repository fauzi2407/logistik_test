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

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'komplit']);
    }

    public function getStatusBadgeAttribute(): string
    {
        switch ($this->status) {
            case 'completed':
            case 'komplit':
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300"><i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i> Komplit (Lunas)</span>';
            case 'delivered':
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-truck-ramp-box mr-1"></i> Delivered</span>';
            case 'shipped':
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 border border-blue-200"><i class="fa-solid fa-truck-fast mr-1"></i> Shipped</span>';
            case 'processing':
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-cyan-100 text-cyan-800 border border-cyan-200"><i class="fa-solid fa-boxes-packing mr-1"></i> Processing</span>';
            case 'approved':
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-indigo-100 text-indigo-800 border border-indigo-200"><i class="fa-solid fa-check mr-1"></i> Approved</span>';
            case 'pending':
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-200"><i class="fa-solid fa-clock mr-1"></i> Pending</span>';
            case 'cancelled':
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-200"><i class="fa-solid fa-ban mr-1"></i> Cancelled</span>';
            case 'draft':
            default:
                return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-800 border border-slate-200">' . htmlspecialchars(strtoupper($this->status ?? 'DRAFT')) . '</span>';
        }
    }

    public function isPickedUp(): bool
    {
        if (in_array($this->status, ['shipped', 'delivered', 'completed', 'komplit'])) {
            return true;
        }

        return $this->items()->whereHas('shipment', function ($q) {
            $q->whereIn('status', ['picked_up', 'in_sorting_hub', 'in_transit', 'out_for_delivery', 'delivered']);
        })->exists();
    }
}
