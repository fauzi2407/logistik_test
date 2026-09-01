<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'chart_of_account_id',
        'debit_amount',
        'credit_amount',
        'memo',
    ];

    protected function casts(): array
    {
        return [
            'debit_amount' => 'float',
            'credit_amount' => 'float',
        ];
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
