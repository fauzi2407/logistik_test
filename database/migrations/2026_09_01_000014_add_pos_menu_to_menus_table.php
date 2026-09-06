<?php

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $menu = Menu::firstOrCreate(
            ['route' => 'pos.index'],
            [
                'title' => 'Menu Kasir / POS Resi',
                'route' => 'pos.index',
                'icon' => 'fa-solid fa-cash-register',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();
        $staffRole = Role::where('slug', 'staff')->first();

        if ($adminRole) {
            RoleMenuPermission::firstOrCreate(
                ['role_id' => $adminRole->id, 'menu_id' => $menu->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    'can_view_salary' => true,
                    'can_edit_salary' => true,
                ]
            );
        }

        if ($staffRole) {
            RoleMenuPermission::firstOrCreate(
                ['role_id' => $staffRole->id, 'menu_id' => $menu->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => false,
                    'can_view_salary' => true,
                    'can_edit_salary' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        $menu = Menu::where('route', 'pos.index')->first();
        if ($menu) {
            RoleMenuPermission::where('menu_id', $menu->id)->delete();
            $menu->delete();
        }
    }
};
