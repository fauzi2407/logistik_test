<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierAssignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_assignment_id',
        'shipment_id',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(CourierAssignment::class, 'courier_assignment_id');
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
