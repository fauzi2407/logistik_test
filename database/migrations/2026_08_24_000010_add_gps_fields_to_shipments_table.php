<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('pod_latitude')->nullable()->after('pod_delivered_at');
            $table->string('pod_longitude')->nullable()->after('pod_latitude');
            $table->string('pod_location_name')->nullable()->after('pod_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['pod_latitude', 'pod_longitude', 'pod_location_name']);
        });
    }
};
