<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_code',
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'province',
        'city',
        'district',
        'subdistrict',
        'postal_code',
        'customer_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
