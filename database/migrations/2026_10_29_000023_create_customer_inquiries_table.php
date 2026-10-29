<?php

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoleMenuPermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_inquiries')) {
            Schema::create('customer_inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone', 50);
                $table->text('message');
                $table->string('status', 30)->default('new'); // new, in_progress, resolved, closed
                $table->text('follow_up_notes')->nullable();
                $table->foreignId('followed_up_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('followed_up_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('menus')) {
            $menu = Menu::firstOrCreate(
                ['route' => 'customer-inquiries.index'],
                [
                    'title' => 'Pesan & Follow Up CS',
                    'route' => 'customer-inquiries.index',
                    'icon' => 'fa-solid fa-headset',
                    'sort_order' => 4,
                    'is_active' => true,
                ]
            );

            if (Schema::hasTable('roles') && Schema::hasTable('role_menu_permissions')) {
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
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('menus')) {
            $menu = Menu::where('route', 'customer-inquiries.index')->first();
            if ($menu) {
                RoleMenuPermission::where('menu_id', $menu->id)->delete();
                $menu->delete();
            }
        }

        Schema::dropIfExists('customer_inquiries');
    }
};

