<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_code',
        'courier_id',
        'period_month',
        'period_year',
        'total_deliveries',
        'basic_salary',
        'commission_per_delivery',
        'total_commission',
        'bonus_amount',
        'deduction_amount',
        'net_salary',
        'status',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'period_month' => 'integer',
            'period_year' => 'integer',
            'total_deliveries' => 'integer',
            'basic_salary' => 'float',
            'commission_per_delivery' => 'float',
            'total_commission' => 'float',
            'bonus_amount' => 'float',
            'deduction_amount' => 'float',
            'net_salary' => 'float',
        ];
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$this->period_month] ?? 'Bulan ' . $this->period_month;
    }
}
