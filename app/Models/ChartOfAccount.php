<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'normal_balance',
        'parent_id',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function initialBalances()
    {
        return $this->hasMany(AccountingInitialBalance::class, 'chart_of_account_id');
    }

    public function journalItems()
    {
        return $this->hasMany(JournalEntryItem::class, 'chart_of_account_id');
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->account_type) {
            'Asset' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'Liability' => 'bg-amber-100 text-amber-800 border-amber-300',
            'Equity' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'Revenue' => 'bg-sky-100 text-sky-800 border-sky-300',
            'Expense' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
