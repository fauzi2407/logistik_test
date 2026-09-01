<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('delivery_order_id');
            $table->string('recipient_phone')->nullable()->after('recipient_name');
            $table->string('recipient_city')->nullable()->after('recipient_phone');
            $table->text('recipient_address')->nullable()->after('recipient_city');
            $table->string('account_ref')->nullable()->after('recipient_address'); // Ref No. Kartu Kredit / Account / Tagihan Bank
            $table->string('tracking_number')->nullable()->after('account_ref'); // Resi AWB otomatis per tujuan
        });

        // Make recipient fields on delivery_orders optional since items contain multi-recipients
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->change();
            $table->string('recipient_phone')->nullable()->change();
            $table->text('recipient_address')->nullable()->change();
            $table->string('recipient_city')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_phone',
                'recipient_city',
                'recipient_address',
                'account_ref',
                'tracking_number',
            ]);
        });
    }
};
