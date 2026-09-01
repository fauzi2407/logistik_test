<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_menu_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('role_menu_permissions', 'can_view_salary')) {
                $table->boolean('can_view_salary')->default(true)->after('can_delete');
            }
            if (!Schema::hasColumn('role_menu_permissions', 'can_edit_salary')) {
                $table->boolean('can_edit_salary')->default(true)->after('can_view_salary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('role_menu_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('role_menu_permissions', 'can_view_salary')) {
                $table->dropColumn(['can_view_salary', 'can_edit_salary']);
            }
        });
    }
};
