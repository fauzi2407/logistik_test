<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCourier(): bool
    {
        return $this->role === 'courier';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function customerProfile()
    {
        return $this->hasOne(Customer::class);
    }

    public function courierProfile()
    {
        return $this->hasOne(Courier::class);
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'slug');
    }

    public function hasPermission($menuRoute, $action = 'view'): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        $roleObj = $this->roleModel;
        if ($roleObj) {
            return $roleObj->hasPermission($menuRoute, $action);
        }

        return false;
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(AppNotification::class)->whereNull('read_at')->latest();
    }

    public function unreadNotificationsCount(): int
    {
        return $this->hasMany(AppNotification::class)->whereNull('read_at')->count();
    }
}
