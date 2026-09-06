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
        'origin_hub_id',
        'destination_hub_id',
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

    public function originHub()
    {
        return $this->belongsTo(BranchHub::class, 'origin_hub_id');
    }

    public function destinationHub()
    {
        return $this->belongsTo(BranchHub::class, 'destination_hub_id');
    }

    public function items()
    {
        return $this->hasMany(CourierAssignmentItem::class);
    }

    public static function markShipmentTaskCompleted($shipmentId)
    {
        $items = CourierAssignmentItem::where('shipment_id', $shipmentId)->get();
        foreach ($items as $item) {
            $item->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $assignment = $item->courierAssignment;
            if ($assignment && $assignment->status !== 'completed' && $assignment->status !== 'cancelled') {
                // Check if all items in this assignment are completed
                $hasPendingItems = CourierAssignmentItem::where('courier_assignment_id', $assignment->id)
                    ->where('status', '!=', 'completed')
                    ->exists();

                if (!$hasPendingItems) {
                    $assignment->update(['status' => 'completed']);

                    // Release courier & vehicle if no other in_progress assignments
                    if ($assignment->courier_id) {
                        $hasOtherCourierAssignments = CourierAssignment::where('courier_id', $assignment->courier_id)
                            ->where('id', '!=', $assignment->id)
                            ->where('status', 'in_progress')
                            ->exists();
                        if (!$hasOtherCourierAssignments) {
                            Courier::where('id', $assignment->courier_id)->update(['status' => 'available']);
                        }
                    }

                    if ($assignment->vehicle_id) {
                        $hasOtherVehicleAssignments = CourierAssignment::where('vehicle_id', $assignment->vehicle_id)
                            ->where('id', '!=', $assignment->id)
                            ->where('status', 'in_progress')
                            ->exists();
                        if (!$hasOtherVehicleAssignments) {
                            Vehicle::where('id', $assignment->vehicle_id)->update(['status' => 'active']);
                        }
                    }
                }
            }
        }
    }
}
