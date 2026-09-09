<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Send notification to recipients and ALWAYS copy to all Admins.
     */
    public static function send(array $params): array
    {
        $userIds = [];

        // 1. Process explicit user(s)
        if (!empty($params['users'])) {
            $users = is_array($params['users']) || $params['users'] instanceof \Illuminate\Support\Collection 
                ? $params['users'] 
                : [$params['users']];

            foreach ($users as $u) {
                if ($u instanceof User) {
                    $userIds[] = $u->id;
                } elseif (is_numeric($u)) {
                    $userIds[] = (int) $u;
                }
            }
        }

        // 2. Process roles if specified
        if (!empty($params['roles'])) {
            $roles = (array) $params['roles'];
            $roleUserIds = User::whereIn('role', $roles)->pluck('id')->toArray();
            $userIds = array_merge($userIds, $roleUserIds);
        }

        // 3. MANDATORY REQUIREMENT: Admin receives ALL notifications
        $adminUserIds = User::where('role', 'admin')->pluck('id')->toArray();
        $finalRecipientIds = array_unique(array_merge($userIds, $adminUserIds));

        if (empty($finalRecipientIds)) {
            return [];
        }

        $title = $params['title'] ?? 'Pemberitahuan Sistem';
        $message = $params['message'] ?? '';
        $type = $params['type'] ?? 'general';
        $icon = $params['icon'] ?? 'fa-bell';
        $color = $params['color'] ?? 'indigo';
        $url = $params['url'] ?? null;
        $data = $params['data'] ?? null;

        $createdNotifications = [];
        $now = now();

        foreach ($finalRecipientIds as $uid) {
            $createdNotifications[] = static::create([
                'user_id' => $uid,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'icon' => $icon,
                'color' => $color,
                'url' => $url,
                'data' => $data,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $createdNotifications;
    }

    /**
     * Convenience helper to send to a Courier (and all admins)
     */
    public static function sendToCourier($courier, array $params): array
    {
        $userId = null;
        if ($courier instanceof User) {
            $userId = $courier->id;
        } elseif ($courier instanceof Courier) {
            $userId = $courier->user_id;
        } elseif (is_numeric($courier)) {
            $courierObj = Courier::find($courier);
            if ($courierObj && $courierObj->user_id) {
                $userId = $courierObj->user_id;
            } else {
                $courierByUser = Courier::where('user_id', $courier)->first();
                if ($courierByUser) {
                    $userId = (int) $courier;
                } else {
                    $user = User::find($courier);
                    if ($user) {
                        $userId = $user->id;
                    }
                }
            }
        }

        if ($userId) {
            $params['users'] = [$userId];
        }
        $params['type'] = $params['type'] ?? 'courier';
        return static::send($params);
    }

    /**
     * Convenience helper to send to a Customer (and all admins)
     */
    public static function sendToCustomer($customer, array $params): array
    {
        $userId = null;
        if ($customer instanceof User) {
            $userId = $customer->id;
        } elseif ($customer instanceof Customer) {
            if ($customer->user_id) {
                $userId = $customer->user_id;
            } elseif ($customer->email) {
                $user = User::where('email', $customer->email)->first();
                if ($user) $userId = $user->id;
            }
        } elseif (is_numeric($customer)) {
            $custObj = Customer::find($customer);
            if ($custObj) {
                if ($custObj->user_id) {
                    $userId = $custObj->user_id;
                } elseif ($custObj->email) {
                    $user = User::where('email', $custObj->email)->first();
                    if ($user) $userId = $user->id;
                }
            } else {
                $user = User::find($customer);
                if ($user) {
                    $userId = $user->id;
                }
            }
        }

        if ($userId) {
            $params['users'] = [$userId];
        }
        $params['type'] = $params['type'] ?? 'customer';
        return static::send($params);
    }

    /**
     * Convenience helper to send to Staff (and all admins)
     */
    public static function sendToStaff(array $params): array
    {
        $params['roles'] = ['staff'];
        $params['type'] = $params['type'] ?? 'staff';
        return static::send($params);
    }
}
