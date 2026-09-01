<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            if (!Schema::hasColumn('couriers', 'basic_salary')) {
                $table->decimal('basic_salary', 12, 2)->default(0)->after('commission_per_delivery');
            }
            if (!Schema::hasColumn('couriers', 'province')) {
                $table->string('province', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('couriers', 'city')) {
                $table->string('city', 100)->nullable()->after('province');
            }
            if (!Schema::hasColumn('couriers', 'district')) {
                $table->string('district', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('couriers', 'subdistrict')) {
                $table->string('subdistrict', 100)->nullable()->after('district');
            }
            if (!Schema::hasColumn('couriers', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('subdistrict');
            }
        });
    }

    public function down(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn(['basic_salary', 'province', 'city', 'district', 'subdistrict', 'postal_code']);
        });
    }
};
