<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            if (!Schema::hasColumn('couriers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('emergency_phone');
            }
            if (!Schema::hasColumn('couriers', 'ktp_number')) {
                $table->string('ktp_number', 50)->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('couriers', 'ktp_photo')) {
                $table->string('ktp_photo')->nullable()->after('ktp_number');
            }
            if (!Schema::hasColumn('couriers', 'ijazah_photo')) {
                $table->string('ijazah_photo')->nullable()->after('ktp_photo');
            }
            if (!Schema::hasColumn('couriers', 'latest_photo')) {
                $table->string('latest_photo')->nullable()->after('ijazah_photo');
            }
            if (!Schema::hasColumn('couriers', 'vehicle_photo')) {
                $table->string('vehicle_photo')->nullable()->after('latest_photo');
            }
            if (!Schema::hasColumn('couriers', 'stnk_photo')) {
                $table->string('stnk_photo')->nullable()->after('vehicle_photo');
            }
            if (!Schema::hasColumn('couriers', 'sim_photo')) {
                $table->string('sim_photo')->nullable()->after('stnk_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date',
                'ktp_number',
                'ktp_photo',
                'ijazah_photo',
                'latest_photo',
                'vehicle_photo',
                'stnk_photo',
                'sim_photo'
            ]);
        });
    }
};
