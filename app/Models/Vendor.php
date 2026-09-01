<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_code',
        'name',
        'phone',
        'email',
        'address',
        'contact_person',
        'bank_name',
        'bank_account_number',
        'status',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
