<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_orders', 'origin_hub_id')) {
                $table->foreignId('origin_hub_id')->nullable()->after('customer_id')->constrained('branch_hubs')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_orders', 'origin_hub_id')) {
                $table->dropForeign(['origin_hub_id']);
                $table->dropColumn('origin_hub_id');
            }
        });
    }
};
