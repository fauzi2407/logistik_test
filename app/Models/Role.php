<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function permissions()
    {
        return $this->hasMany(RoleMenuPermission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }

    public function hasPermission($menuRoute, $action = 'view')
    {
        if ($this->slug === 'admin') {
            return true;
        }

        $menu = Menu::where('route', $menuRoute)->first();
        if (!$menu && str_contains($menuRoute, '.')) {
            $parts = explode('.', $menuRoute);
            $indexRoute = $parts[0] . '.index';
            $menu = Menu::where('route', $indexRoute)->first();
        }

        if (!$menu) {
            return true;
        }

        $permission = RoleMenuPermission::where('role_id', $this->id)
            ->where('menu_id', $menu->id)
            ->first();

        if (!$permission) {
            return false;
        }

        switch ($action) {
            case 'create':
                return (bool) $permission->can_create;
            case 'edit':
            case 'update':
                return (bool) $permission->can_edit;
            case 'delete':
            case 'destroy':
                return (bool) $permission->can_delete;
            case 'view_salary':
            case 'view_gaji':
                return (bool) ($permission->can_view_salary ?? true);
            case 'edit_salary':
            case 'edit_gaji':
                return (bool) ($permission->can_edit_salary ?? true);
            case 'view':
            default:
                return (bool) $permission->can_view;
        }
    }
}
