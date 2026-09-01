<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('courier_payrolls', 'basic_salary')) {
                $table->decimal('basic_salary', 12, 2)->default(0)->after('total_deliveries');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courier_payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('courier_payrolls', 'basic_salary')) {
                $table->dropColumn('basic_salary');
            }
        });
    }
};
