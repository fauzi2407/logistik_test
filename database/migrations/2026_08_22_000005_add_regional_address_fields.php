<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->string('recipient_province', 100)->nullable()->after('recipient_phone');
            $table->string('recipient_district', 100)->nullable()->after('recipient_city');
            $table->string('recipient_subdistrict', 100)->nullable()->after('recipient_district');
            $table->string('recipient_postal_code', 20)->nullable()->after('recipient_subdistrict');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->string('recipient_province', 100)->nullable()->after('recipient_phone');
            $table->string('recipient_district', 100)->nullable()->after('recipient_city');
            $table->string('recipient_subdistrict', 100)->nullable()->after('recipient_district');
            $table->string('recipient_postal_code', 20)->nullable()->after('recipient_subdistrict');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->dropColumn(['recipient_province', 'recipient_district', 'recipient_subdistrict', 'recipient_postal_code']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['recipient_province', 'recipient_district', 'recipient_subdistrict', 'recipient_postal_code']);
        });
    }
};
