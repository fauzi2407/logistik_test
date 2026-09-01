<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_number',
        'courier_id',
        'vehicle_id',
        'assignment_type',
        'assignment_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assignment_date' => 'date',
        ];
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items()
    {
        return $this->hasMany(CourierAssignmentItem::class);
    }
}
