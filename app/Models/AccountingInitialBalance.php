<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingInitialBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'chart_of_account_id',
        'fiscal_year',
        'debit_amount',
        'credit_amount',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_year' => 'integer',
            'debit_amount' => 'float',
            'credit_amount' => 'float',
        ];
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
