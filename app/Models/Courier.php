<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_hub_id',
        'vehicle_id',
        'courier_code',
        'name',
        'phone',
        'address',
        'province',
        'city',
        'district',
        'subdistrict',
        'postal_code',
        'emergency_phone',
        'age',
        'basic_salary',
        'commission_per_delivery',
        'license_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branchHub()
    {
        return $this->belongsTo(BranchHub::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function assignments()
    {
        return $this->hasMany(CourierAssignment::class);
    }
}
