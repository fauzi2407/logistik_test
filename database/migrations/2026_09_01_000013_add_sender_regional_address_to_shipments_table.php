<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            if (!Schema::hasColumn('shipments', 'sender_province')) {
                $table->string('sender_province', 100)->nullable()->after('sender_phone');
            }
            if (!Schema::hasColumn('shipments', 'sender_district')) {
                $table->string('sender_district', 100)->nullable()->after('sender_city');
            }
            if (!Schema::hasColumn('shipments', 'sender_subdistrict')) {
                $table->string('sender_subdistrict', 100)->nullable()->after('sender_district');
            }
            if (!Schema::hasColumn('shipments', 'sender_postal_code')) {
                $table->string('sender_postal_code', 20)->nullable()->after('sender_subdistrict');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['sender_province', 'sender_district', 'sender_subdistrict', 'sender_postal_code']);
        });
    }
};
