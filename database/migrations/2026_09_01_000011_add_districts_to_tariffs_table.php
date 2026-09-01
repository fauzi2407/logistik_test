<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('origin_district')->nullable()->after('origin_city');
            $table->string('origin_subdistrict')->nullable()->after('origin_district');
            $table->string('destination_district')->nullable()->after('destination_city');
            $table->string('destination_subdistrict')->nullable()->after('destination_district');
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn(['origin_district', 'origin_subdistrict', 'destination_district', 'destination_subdistrict']);
        });
    }
};
