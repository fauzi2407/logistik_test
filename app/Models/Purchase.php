<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'purchase_date',
        'vendor_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'subtotal' => 'float',
            'tax_amount' => 'float',
            'discount_amount' => 'float',
            'total_amount' => 'float',
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'received' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'ordered' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'draft' => 'bg-amber-100 text-amber-800 border-amber-300',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getPaymentBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'partial' => 'bg-amber-100 text-amber-800 border-amber-300',
            'unpaid' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
