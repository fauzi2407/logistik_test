<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_number',
        'entry_date',
        'reference_number',
        'description',
        'total_debit',
        'total_credit',
        'is_closing_entry',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'total_debit' => 'float',
            'total_credit' => 'float',
            'is_closing_entry' => 'boolean',
        ];
    }

    public function items()
    {
        return $this->hasMany(JournalEntryItem::class, 'journal_entry_id');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
