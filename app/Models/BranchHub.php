<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchHub extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'city',
        'address',
        'phone',
        'person_in_charge',
    ];
}
