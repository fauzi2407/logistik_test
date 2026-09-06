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
        'ownership_type',
        'capacity_kg',
        'asset_value',
        'status',
        'journal_entry_id',
    ];

    public function couriers()
    {
        return $this->hasMany(Courier::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
