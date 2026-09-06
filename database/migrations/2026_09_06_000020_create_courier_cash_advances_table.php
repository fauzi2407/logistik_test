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
        Schema::create('courier_cash_advances', function (Blueprint $table) {
            $table->id();
            $table->string('advance_number', 50)->unique();
            $table->foreignId('courier_id')->constrained('couriers')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->date('request_date');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'settled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained('courier_payrolls')->onDelete('set null');
            $table->timestamps();
        });

        // Register Menu in System
        $menu = Menu::firstOrCreate(
            ['route' => 'courier-cash-advances.index'],
            [
                'title' => 'Kasbon Kurir',
                'route' => 'courier-cash-advances.index',
                'icon' => 'fa-solid fa-hand-holding-dollar',
                'sort_order' => 8,
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
        $menu = Menu::where('route', 'courier-cash-advances.index')->first();
        if ($menu) {
            RoleMenuPermission::where('menu_id', $menu->id)->delete();
            $menu->delete();
        }

        Schema::dropIfExists('courier_cash_advances');
    }
};
