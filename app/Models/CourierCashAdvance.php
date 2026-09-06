<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierCashAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'advance_number',
        'courier_id',
        'amount',
        'request_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'payroll_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'request_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payroll()
    {
        return $this->belongsTo(CourierPayroll::class, 'payroll_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isSettled(): bool
    {
        return $this->status === 'settled';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'settled' => 'Lunas (Dipotong Gaji)',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            'settled' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
