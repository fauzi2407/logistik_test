<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'status',
        'follow_up_notes',
        'followed_up_by',
        'followed_up_at',
    ];

    protected $casts = [
        'followed_up_at' => 'datetime',
    ];

    public function followedUpByUser()
    {
        return $this->belongsTo(User::class, 'followed_up_by');
    }

    public function getWhatsappUrlAttribute(): string
    {
        $rawPhone = preg_replace('/[^0-9]/', '', $this->phone ?? '');
        if (str_starts_with($rawPhone, '0')) {
            $rawPhone = '62' . substr($rawPhone, 1);
        } elseif (!str_starts_with($rawPhone, '62')) {
            $rawPhone = '62' . $rawPhone;
        }

        $greeting = "Halo Bapak/Ibu {$this->name}, kami dari Tim Layanan Pelanggan PT. Antaran Warga Ekspress (AWX) menindaklanjuti pesan/kebutuhan pengiriman yang Anda kirimkan melalui website kami.";

        return "https://wa.me/{$rawPhone}?text=" . urlencode($greeting);
    }
}

