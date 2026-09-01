<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'vehicle_type',
        'capacity_kg',
        'status',
    ];

    public function couriers()
    {
        return $this->hasMany(Courier::class);
    }
}
