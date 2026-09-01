<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalYearClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year',
        'closed_at',
        'net_profit_loss',
        'closing_journal_entry_id',
        'closed_by_user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_year' => 'integer',
            'closed_at' => 'datetime',
            'net_profit_loss' => 'float',
        ];
    }

    public function closingJournalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'closing_journal_entry_id');
    }

    public function closedUser()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }
}
